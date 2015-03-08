/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */
var app = {
    // Application Constructor
    initialize: function() {
        this.bindEvents();
    },
    // Bind Event Listeners
    //
    // Bind any events that are required on startup. Common events are:
    // 'load', 'deviceready', 'offline', and 'online'.
    bindEvents: function() {
        document.addEventListener('deviceready', this.onDeviceReady, false);
    },
    // deviceready Event Handler
    //
    // The scope of 'this' is the event. In order to call the 'receivedEvent'
    // function, we must explicitly call 'app.receivedEvent(...);'
    onDeviceReady: function() {
        app.receivedEvent('deviceready');
    },
    // Update DOM on a Received Event
    receivedEvent: function() {
        var test = document.getElementById('goscan');

        test.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('scanning');
            cordova.plugins.barcodeScanner.scan(
                function (result) {
                    alert("We got a barcode\n" +
                        "Resulta: " + result.text + "\n" +
                        "Format: " + result.format + "\n" +
                        "Cancelled: " + result.cancelled);

                    $.ajax({

                        url: "http://api.edibleapp.fr/match/"+result.text+"/1", // l'url
                        type: "POST", // la méthode
                        data: '', // sérialisation de données : username=test&password=test
                        dataType:'json', //type de données, permet de parser le JSON
                        success: function(msg) {

                            var main = document.getElementById('main'),
                                main_scanko = document.getElementById('main_scanko'),
                                main_scanok = document.getElementById('main_scanok');

                            if (msg.result.matching.traces != "" && msg.result.matching.allergens != "") {

                                var allergen = document.getElementById("allergen"),
                                    allergenName = document.createTextNode("Nutella");

                                allergen.appendChild(allergenName);

                                main.style.display = "none";
                                main_scanko.style.display = 'block';

                                alert("Nom du product : "+msg.result.product.name);
                            } else {

                                main.style.display = "none";
                                main_scanok.display = "block";

                                alert("Nom du product : "+msg.result.product.name);
                            }
                        },
                        error: function( jqXhr, textStatus, errorThrown ){
                            alert("error:"+textStatus+errorThrown );
                        }
                    });
                },
                function (error) {
                    alert("Scanning failed: " + error);
                }
            );
        }, false);
    }
};

app.initialize();