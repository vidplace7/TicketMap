# Geocode all addresses with valid addresses in the database #
# Geocoding is the process of encoding a human-readable address into a set of coordinates (lat, long) #
from os import getenv
import pymssql, sys, json

from geopy.geocoders import MapQuest
geolocator = MapQuest('API_KEY')##MapQuest API Key

conn = pymssql.connect(server='sql01', user='website', password='7a6z9qf3', database='Tigerpaw11', port='1433')
cursor = conn.cursor(as_dict=True)

# Select attributes of Accounts with valid addresses
cursor.execute("SELECT Address1,City,State,AccountNumber,AccountName,AccountType FROM dbo.tblAccounts WHERE (Address1 IS NOT NULL AND Address1 NOT LIKE '%P%O% Box%') AND City IS NOT NULL and State IS NOT NULL ORDER BY AccountNumber")
row = cursor.fetchone()
addresses = {}
# Iterate through each row, geocoding the address and outputting to a JSON file
for row in cursor:
	# Combine Address1, City, and State to make the "address1" variable
	address1 = row['Address1'] + " "
	address1 += row['City'] + ", "
	address1 += row['State']
	# Geocode the "address1" string, outputting the "latitude" and "longitude" arrays
	address, (latitude, longitude) = geolocator.geocode(address1)
	# Create dictionary "addresses" listing Accounts by AccountNumber (key) with properties "name", "lat", lng", "address", and "type"
	addresses[row['AccountNumber']] = {'name': row['AccountName'], 'lat': latitude, 'lng': longitude, 'address': address1, 'type': row['AccountType']}

# Dump the dictionary "addresses" into a json file, pretty print stylized!
with open('codes.json', 'w') as outfile:
	json.dump(addresses, outfile, sort_keys=True, indent=4, separators=(',', ': '))