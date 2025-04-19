from flask import Flask, render_template, jsonify
import cv2
from pyzbar.pyzbar import decode
import mysql.connector

app = Flask(__name__)

# MySQL Database Connection
db_config = {
    "host": "localhost",
    "user": "root",
    "password": "MYSQL",
    "database": "inventory_db2"
}

# Function to scan barcode using OpenCV
def scan_barcode():
    cap = cv2.VideoCapture(0)
    cap.set(3, 640)
    cap.set(4, 480)

    scanned_data = None

    while True:
        success, frame = cap.read()
        if not success:
            return "❌ Camera error!"

        for barcode in decode(frame):
            scanned_data = barcode.data.decode('utf-8')
            cap.release()
            cv2.destroyAllWindows()
            return scanned_data  # Return barcode value
        
        cv2.imshow("Scanning...", frame)
        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

    cap.release()
    cv2.destroyAllWindows()
    return scanned_data

# Function to fetch product details from MySQL
def get_product_details(barcode):
    conn = mysql.connector.connect(**db_config)
    cursor = conn.cursor()

    query = "SELECT name, category, price, quantity FROM products WHERE barcode = %s"
    cursor.execute(query, (barcode,))
    product = cursor.fetchone()

    conn.close()

    if product:
        return {
            "name": product[0],
            "category": product[1],
            "price": product[2],
            "quantity": product[3]
        }
    else:
        return None

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/scan', methods=['GET'])
def scan():
    barcode_value = scan_barcode()
    product_info = get_product_details(barcode_value)

    if product_info:
        return jsonify({"status": "success", "product": product_info})
    else:
        return jsonify({"status": "error", "message": "❌ Product not found!"})

if __name__ == "__main__":
    app.run(debug=True)
