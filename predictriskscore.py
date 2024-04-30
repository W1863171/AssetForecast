import pandas as pd
import joblib
import mysql.connector
from sklearn.preprocessing import OrdinalEncoder
from datetime import datetime

# Load the dataset from the database
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="", 
    database="AssetForecast"
)

# Function to fetch asset data from the database
def fetch_asset_data(barcode):
    cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT barcode, assetType, assetCondition, assetRepairCategory, environmentalRating, assetLifeExpectancy, assetAge, installationDate FROM asset WHERE barcode = %s", (barcode,))
    data = cursor.fetchone()
    cursor.close()
    return data

# Function to calculate the risk score for an individual asset
def calculate_risk_score(data):
    # Load the encoder
    encoder = joblib.load('../AssetForecast/ML/RiskScore_ordinal_encoder.pkl')

    # Ensure feature names are provided when transforming
    categorical_cols = ['assetType', 'assetCondition', 'assetRepairCategory']
    encoded_data = encoder.transform([[data[col] for col in categorical_cols]])

    # Format the datetime object as dd/mm/yyyy
    formatted_date = data['installationDate'].strftime("%d/%m/%Y")

    # Convert installationDate to Unix timestamp
    installation_date_unix = pd.to_datetime(formatted_date, format="%d/%m/%Y").timestamp()  

    # Combine encoded categorical data with numerical data in the correct order
    X = pd.DataFrame([list(encoded_data[0])+[data['assetLifeExpectancy'], installation_date_unix, data['assetAge'], data['environmentalRating']]])

    # Load the MinMaxScaler
    scaler = joblib.load('../AssetForecast/ML/RiskScore_Scaler.pkl')

    # Scale the input data
    X_scaled = scaler.transform(X)

    # Load the trained Random Forest model
    model = joblib.load('../AssetForecast/ML/random_forest_modelRiskScore.pkl')

    # Predict the risk score
    new_risk_score = model.predict(X_scaled)[0]
    return new_risk_score

# Function to update the database with the new risk score
def update_risk_score(barcode, new_risk_score):
    cursor = conn.cursor()
    cursor.execute("UPDATE asset SET riskScore = %s WHERE barcode = %s", (new_risk_score, barcode))
    conn.commit()
    cursor.close()

# Main function to handle the prediction process
def main(barcode):
    # Fetch asset data from the database
    asset_data = fetch_asset_data(barcode)
    
    if asset_data:
        # Calculate the new risk score
        new_risk_score = calculate_risk_score(asset_data)

        # Update the database with the new risk score
        update_risk_score(barcode, new_risk_score)

        # Return a message with the updated risk score
        print(f"New risk score for asset {barcode} is {new_risk_score}")

        # After updating the risk score in the database
        

    else:
        return "Asset not found in the database"

# Call the main function with the barcode parameter
if __name__ == "__main__":
    import sys
    barcode = sys.argv[1]
    main(barcode)
