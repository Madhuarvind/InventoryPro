import cv2
from pyzbar.pyzbar import decode

def scan_barcode():
    cap = cv2.VideoCapture(0)  # Open camera
    cap.set(3, 640)  # Set width
    cap.set(4, 480)  # Set height

    while True:
        success, frame = cap.read()
        if not success:
            print("❌ Camera not detected!")
            break
        
        for barcode in decode(frame):
            barcode_data = barcode.data.decode('utf-8')
            print(f"✅ Barcode Scanned: {barcode_data}")
            cap.release()
            cv2.destroyAllWindows()
            return barcode_data  # Return scanned data
        
        cv2.imshow("Barcode Scanner", frame)
        if cv2.waitKey(1) & 0xFF == ord('q'):  # Press 'q' to quit
            break

    cap.release()
    cv2.destroyAllWindows()

# Run scanner
if __name__ == "__main__":
    scan_barcode()
