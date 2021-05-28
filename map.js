document.addEventListener("DOMContentLoaded", function(event) {
    let coords = document.querySelector('.coords').getAttribute('data-attr');
    let names = document.querySelector('.names').getAttribute('data-attr');
    let desc = document.querySelector('.desc').getAttribute('data-attr');

    console.log(coords[0]);

    let options = {
        zoom: 8,
        center: {lat: 49.5850, lng: 36.1409},
    }
    let map = new google.maps.Map(document.getElementById('map'), options);
    let i;
    for (i = 0; i < coords.length; i++) {
        let description = desc[i];
        let name = names[i];

        axios.get('https://maps.googleapis.com/maps/api/geocode/json', {
            params: {
                address: coords[i],
                key: 'AIzaSyAFKGM4i-IihJp62mQ9sAbHJG0WzfyTJQg'
            }
        }).then(function (response) {
            // Log full response
            console.log(response);
            let lat = response.data.results[0].geometry.location.lat;
            let lng = response.data.results[0].geometry.location.lng;

            let coord = {lat: lat, lng: lng};
            for (i = 0; i < coords.length; i++) {
                const infowindow = new google.maps.InfoWindow({
                    content: 'Store Name: ' + name + '<br>' + description,
                });

                let marker;
                marker = new google.maps.Marker({
                    position: coord,
                    map: map,
                    title: name,
                });
                marker.addListener("click", () => {
                    infowindow.open(map, marker);
                });
            }
        });
    }
});