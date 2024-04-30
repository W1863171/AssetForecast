import pandas as pd
import joblib
import mysql.connector
from sklearn.preprocessing import OrdinalEncoder
from datetime import datetime, timedelta

# connect to the database
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="", 
    database="AssetForecast"
)

# Function to fetch all asset data from the database
def fetch_all_asset_data():
    cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT a.*, m.* FROM asset a LEFT JOIN maintenance_task m ON a.barcode = m.barcode")
    data = cursor.fetchall()
    cursor.close()
    return data

# Function to fetch asset data from the database for a specific barcode
def fetch_asset_data(barcode):
    cursor = conn.cursor(dictionary=True)
    cursor.execute("""
        SELECT a.*, m.* 
        FROM asset a 
        LEFT JOIN maintenance_task m ON a.barcode = m.barcode 
        WHERE a.barcode = %s
    """, (barcode,))
    data = cursor.fetchall()  # Fetch all rows
    cursor.close()
    return data

# Update the database with the predicted task type and due date
def update_database_with_predicted_task(barcode, task_type, due_date):
    cursor = conn.cursor()

    # load label encoder
    label_encoder = joblib.load('../AssetForecast/ML/label_encoder.joblib')
    # Get the original labels from the label encoder
    original_labels = label_encoder.inverse_transform(range(len(label_encoder.classes_)))
    # Create a reverse mapping dictionary
    reverse_mapping = dict(zip(label_encoder.classes_, original_labels))

    # for key, value in reverse_mapping.items():
        # print(f"Encoded value: {key}, Task type: {value}")

    # print(reverse_mapping)
    # print(task_type)


    # Decode the predicted task type using the reverse mapping dictionary
    task_type = list(reverse_mapping.keys())[task_type]

    # Insert a new row into the maintenance_task table with the predicted values
    sql_query = """
        INSERT INTO maintenance_task (barcode, taskStatus, createdDate, taskType, description, requiredBy)
        SELECT 
            %s AS barcode,
            'Not Approved' AS taskStatus,
            NOW() AS createdDate,
            %s AS taskType,
            CONCAT(%s, ' - ', a.assetType, ' - Predicted due within ', %s) AS description,
            %s AS requiredBy
        FROM asset AS a
        WHERE a.barcode = %s
    """

    # Execute the query
    cursor.execute(sql_query, (barcode, task_type, task_type, due_date.strftime("%Y-%m-%d %H:%M:%S"), due_date, barcode))

    conn.commit()
    cursor.close()

# Function to remove unapproved tasks for a given barcode
def remove_unapproved_tasks(barcode):
    cursor = conn.cursor()
    cursor.execute("DELETE FROM maintenance_task WHERE barcode = %s AND approvedBy IS NULL", (barcode,))
    conn.commit()
    cursor.close()

# Function to predict task type and due date for a given barcode and timeframe
def predict_task_type_and_due_date(barcode, timeframe):
    # Load the trained models and encoder
    clf = joblib.load('../AssetForecast/ML/random_forest_classifier.joblib')
    reg = joblib.load('../AssetForecast/ML/random_forest_regressor.joblib')
    encoder = joblib.load('../AssetForecast/ML/ordinal_encoder.joblib')
    imputer = joblib.load('../AssetForecast/ML/simple_imputer.joblib')

    # Fetch asset data from the database
    asset_data = fetch_asset_data(barcode)

    # Define the columns to drop
    columns_to_drop = ['description', 'startedAt', 'completedAt', 'attendedBy', 'comments', 'approvedBy', 'scheduledBy', 'scheduledDate', 'cancelledBy', 'riskRating', 'locationID', 'installationDate', 'createdDate', 'requiredBy']

    # Define the categorical columns for ordinal encoding
    categorical_cols = ['environmentalRating', 'impactRating', 'assetCondition', 'assetRepairCategory', 'taskStatus', 'taskType', 'assetType']

    if asset_data:
        # Check if asset_data is a dictionary or a list of dictionaries
        if isinstance(asset_data, dict):
            asset_data = [asset_data]
        
        # Convert the list to a DataFrame
        asset_data = pd.DataFrame(asset_data)
        
        # Drop specific columns from the DataFrame
        asset_data.drop(columns=columns_to_drop, inplace=True)

        # Add missing features before imputation
        asset_data['RequiredWithin'] = 0
        asset_data['TypeOfMaintenanceRequired'] = 0
        
        # Encode categorical variables using OrdinalEncoder
        asset_data[categorical_cols] = encoder.transform(asset_data[categorical_cols])

        fit_column_order = ['taskID', 'barcode', 'assetLifeExpectancy', 'assetAge', 'riskScore',
                    'TypeOfMaintenanceRequired', 'RequiredWithin', 'environmentalRating',
                    'impactRating', 'assetCondition', 'assetRepairCategory', 'taskStatus',
                    'taskType', 'assetType']

        # Reorder the columns of asset_data to match the order from fit
        asset_data = asset_data.reindex(columns=fit_column_order)

        # Prepare the data for prediction
        X = asset_data
        X_imputed = imputer.transform(X)

        # Drop added features after imputation
        X_imputed = pd.DataFrame(X_imputed, columns=asset_data.columns)
        X_imputed.drop(columns=['RequiredWithin', 'TypeOfMaintenanceRequired'], inplace=True)

        # Predict task type
        task_type = clf.predict(X_imputed)[0]

        # Predict the number of days until maintenance is required using the regressor model
        days_until_maintenance = int(reg.predict(X_imputed)[0])

        # Predict the number of days until maintenance is required using the regressor model
        days_until_maintenance = int(reg.predict(X_imputed)[0])

        # Calculate the due date based on the predicted number of days until maintenance is required
        due_date = datetime.now() + timedelta(days=days_until_maintenance)

        # Parse the selected timeframe and check if the due date falls within it
        if timeframe == "7_days" and days_until_maintenance <= 7:
            # Due date falls within the next 7 days
            return task_type, due_date
        elif timeframe == "2_weeks" and days_until_maintenance <= 14:
            # Due date falls within the next 2 weeks (14 days)
            return task_type, due_date
        elif timeframe == "1_month" and days_until_maintenance <= 30:
            # Due date falls within the next 1 month (30 days)
            return task_type, due_date
        elif timeframe == "3_months" and days_until_maintenance <= 90:
            # Due date falls within the next 3 months (90 days)
            return task_type, due_date
        elif timeframe == "6_months" and days_until_maintenance <= 180:
            # Due date falls within the next 6 months (180 days)
            return task_type, due_date
        elif timeframe == "1_year" and days_until_maintenance <= 365:
            # Due date falls within the next 1 year (365 days)
            return task_type, due_date
        else:
            return None, None
        
    else:
        return None, None


# Main function to handle the prediction process for all assets
def main(timeframe):
    # Fetch all asset data from the database
    all_asset_data = fetch_all_asset_data()

    # Initialize a set to store processed barcodes
    processed_barcodes = set()

    # Predict task type and due date for each asset
    for asset_data in all_asset_data:
        barcode = asset_data['barcode']
        
        # Check if the barcode has already been processed
        if barcode in processed_barcodes:
            continue  # Skip processing if the barcode has already been processed
        else:
            task_type, due_date = predict_task_type_and_due_date(barcode, timeframe)
            
            # Mark the barcode as processed
            processed_barcodes.add(barcode)

            # Check if task_type and due_date are not None
            if task_type is not None and due_date is not None:
                
                # Remove unapproved tasks for the given barcode
                remove_unapproved_tasks(barcode)

                # Update the database with the predicted task type and due date
                update_database_with_predicted_task(barcode, task_type, due_date)

                # This is for testing only print(f"Predicted task type for asset {barcode}: {task_type}, Due date: {due_date}")
            # else:
                # print(f"No prediction available for asset {barcode}")


    # Print a message indicating completion
    print("Python script execution completed.")
    sys.stdout.flush()  # Flush the stdout buffer

# Call the main function with the timeframe parameter
if __name__ == "__main__":
    import sys
    timeframe = sys.argv[1]
    main(timeframe)
