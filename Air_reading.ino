#include <WiFi.h>
#include <HTTPClient.h>
#include <math.h>

const char* ssid = "Shlok's Galaxy A21s";
const char* password = "Shlok.patel";
const char* serverUrl = "http://192.168.3.113/airlog.php"; 

// PM2.5
const int ledControlPin = 18;
const int dustSensorPin = 36;

// MQ135 
const int gasSensorPin = 39;


#define RL_VALUE 10.0    
#define RZERO 76.63      

void setup() {
  Serial.begin(115200);
  pinMode(ledControlPin, OUTPUT);
  digitalWrite(ledControlPin, HIGH); 

  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nConnected to WiFi.");
}

float getMQ135PPM(int adcValue) {
  float voltage = adcValue * (3.3 / 4095.0); 

  
  if (voltage <= 0.01) return 0.0;

  float rs = ((3.3 * RL_VALUE) / voltage) - RL_VALUE; 
  float ratio = rs / RZERO;

  
  float ppm = 116.6020682 * pow(ratio, -2.769034857);
  return (ppm < 0 || isnan(ppm)) ? 0.0 : ppm;
}

void loop() {
 
  digitalWrite(ledControlPin, LOW);
  delayMicroseconds(280);
  int adcValue = analogRead(dustSensorPin);
  delayMicroseconds(40);
  digitalWrite(ledControlPin, HIGH);
  delayMicroseconds(9680);

  float voltage = adcValue * (3.3 / 4095.0);
  float dustDensity = 0.17 * voltage * 1000.0 - 0.1;
  if (dustDensity < 0) dustDensity = 0;

 
  int gasRaw = analogRead(gasSensorPin);
  float gasPPM = getMQ135PPM(gasRaw);

  
  Serial.println("=== Sensor Readings ===");
  Serial.print("Dust ADC: "); Serial.println(adcValue);
  Serial.print("Dust Voltage: "); Serial.println(voltage, 3);
  Serial.print("Dust (µg/m³): "); Serial.println(dustDensity, 2);
  Serial.print("Gas ADC: "); Serial.println(gasRaw);
  Serial.print("Gas (PPM): "); Serial.println(gasPPM, 2);
  Serial.println("=======================\n");

  
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String url = String(serverUrl) +
                 "?voltage=" + String(voltage, 3) +
                 "&dust=" + String(dustDensity, 2) +
                 "&gas=" + String(gasPPM, 2);

    http.begin(url);
    int httpCode = http.GET();
    if (httpCode > 0) {
      String response = http.getString();
      Serial.println("Server Response: " + response);
    } else {
      Serial.println("Error sending data to server");
    }
    http.end();
  }

  delay(10000); 
}
