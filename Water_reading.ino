#include <OneWire.h>
#include <DallasTemperature.h>

#define ONE_WIRE_BUS 4  

OneWire oneWire(ONE_WIRE_BUS);
DallasTemperature sensors(&oneWire);

void setup() {
  Serial.begin(115200);
  pinMode(ONE_WIRE_BUS, INPUT_PULLUP);
  sensors.begin();
}

void loop() {
  sensors.requestTemperatures(); 
  float temperatureC = sensors.getTempCByIndex(0); 

  if (temperatureC == -127.00) {
    Serial.println("Sensor not detected. Check wiring.");
  } else {
    Serial.print("Water Temperature: ");
    Serial.print(temperatureC);
    Serial.println(" °C");
  }

  delay(2000); 
}
