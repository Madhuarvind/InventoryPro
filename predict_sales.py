from flask import Flask, request, jsonify
import MySQLdb
import numpy as np
import pandas as pd
from sklearn.linear_model import LinearRegression

# Initialize Flask app
app = Flask(__name__)

# Function to connect to MySQL
def get_db_connection():
    try:
        return MySQLdb.connect(
            host="localhost", 
            user="root", 
            passwd="MYSQL", 
            db="inventory_db2", 
            charset='utf8', 
            use_unicode=True
        )
    except MySQLdb.Error as e:
        print(f"Database Connection Error: {e}")
        return None

# Default route to check if the API is running
@app.route('/')
def home():
    return jsonify({"message": "Sales Prediction API is running. Use /predict to get predictions."})

# Sales prediction route
@app.route('/predict', methods=['GET'])
def predict_sales():
    db = get_db_connection()
    
    if db is None:
        return jsonify({"error": "Database connection failed."}), 500

    cursor = db.cursor()

    try:
        # Fetch past sales data from orders and order_items table
        cursor.execute("""
            SELECT DATE(o.order_date), SUM(oi.quantity) 
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            GROUP BY DATE(o.order_date)
            ORDER BY DATE(o.order_date) ASC
        """)
        data = cursor.fetchall()

        if not data:
            return jsonify({"error": "No sales data available."})

        # Process data
        df = pd.DataFrame(data, columns=['date', 'sales'])
        df['date'] = pd.to_datetime(df['date'])
        df['days'] = (df['date'] - df['date'].min()).dt.days

        # Train Linear Regression model
        X = df[['days']]
        y = df['sales']
        model = LinearRegression()
        model.fit(X, y)

        # Predict sales for the next 7 days
        future_days = np.array([[df['days'].max() + i] for i in range(1, 8)])
        predictions = model.predict(future_days)
        result = {f"Day {i+1}": round(pred, 2) for i, pred in enumerate(predictions)}

        return jsonify(result)

    except MySQLdb.Error as db_err:
        return jsonify({"error": f"MySQL Error: {db_err}"}), 500
    except Exception as e:
        return jsonify({"error": f"Unexpected Error: {str(e)}"}), 500
    finally:
        cursor.close()
        db.close()

# Run the Flask app
if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
