<?php

require __DIR__ . "../../../config_admin.php";

if (!boomAllow(100)) {
    exit;
}

echo elementTitle($lang["online"]);
$activeUsers =  onlineMap();

?>
   <style>
 #map {
height: 700px;
 width: 100%;
     z-index: 3;
}
.leaflet-marker-icon {
 border-radius: 50%;
 width: 40px;
 height: 40px;
}       
    </style>
<div class="page_full">
    <div class="page_element">
        <div id="update_list">
               <div id="map"></div>

        </div>
    </div>
</div>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />

    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.3/dist/leaflet.js"></script>

    <script>
        // Check if Leaflet is loaded
        setTimeout(() => {
            if (typeof L !== 'undefined') {
                // Initialize the map
                var map = L.map('map').setView([51.505, -0.09], 13);

                // Set up the OpenStreetMap layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                // Function to get location by IP
                function getLocationByIP(ip) {
                    return fetch(`https://get.geojs.io/v1/ip/geo/${ip}.json`)
                        .then(response => response.json())
                        .then(data => ({
                            lat: data.latitude,
                            lon: data.longitude
                        }))
                        .catch(error => {
                            console.error(`Error fetching location for IP ${ip}:`, error);
                            return { lat: 0, lon: 0 }; // Default to [0, 0] on error
                        });
                }

                // Initialize the map with user locations
                async function initMap() {
                    // Sample PHP variable to JavaScript
                    const activeUsers = <?php echo json_encode($activeUsers); ?>;

                    // Array to hold markers
                    const markers = [];

                    // Array to hold promises for geolocation
                    const userPromises = activeUsers.map(async user => {
                        const location = await getLocationByIP(user.user_ip);
                        // Only add markers if location is valid
                        if (location.lat !== 0 && location.lon !== 0) {
                            const avatarUrl = user.user_tumb; // Assuming you have a user_avatar field with URL
                            const customIcon = L.icon({
                                iconUrl: avatarUrl,
                                iconSize: [40, 40], // Size of the icon
                                iconAnchor: [20, 40], // Point of the icon which will correspond to marker's location
                                popupAnchor: [0, -40] // Point from which the popup should open relative to the iconAnchor
                            });

                            const marker = L.marker([location.lat, location.lon], { icon: customIcon }).bindPopup(`<b>${user.user_name}</b><br><b>Latitude:</b> ${location.lat}<br><b>Longitude:</b> ${location.lon}`);
                            markers.push(marker);
                            marker.addTo(map);
                        }
                    });

                    // Wait for all geolocation requests to complete
                    await Promise.all(userPromises);

                    // Adjust the map view to fit all markers
                    if (markers.length > 0) {
                        var group = new L.featureGroup(markers);
                        map.fitBounds(group.getBounds());
                    } else {
                        // Optional: Center the map on default coordinates if no valid markers
                        map.setView([51.505, -0.09], 13);
                    }
                }

                initMap();
            } else {
                console.error('Leaflet is not loaded.');
            }
        }, 2000); // Delay execution by 1000 milliseconds (1 second)
    </script>


