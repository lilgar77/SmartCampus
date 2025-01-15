/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉')


document.addEventListener('DOMContentLoaded', () => {


    // Vérifiez la structure de roomData
    console.log(window.roomCO2);

    // Extraire les données pertinentes de window.roomData
    const co2Value = window.roomCO2;
    const temp = window.roomTemp;
    const humidity = window.roomHumidity;

    // Exemple d'utilisation des données
    console.log('Humidity:', humidity);
    console.log('Temperature:', temp);
    console.log('CO2:', co2Value);
    // Gestion de l'humidité
    const wavePath = document.getElementById('wave-path');
    const colorTextWave = document.querySelector('.circle-info-wave');

    function updateHumidityColor() {
        if (humidity >= 70) {
            if (temp < 20) {
                colorTextWave.style.color = '#93CC56';
            } else if (temp >= 20 && temp <= 25) {
                colorTextWave.style.color = '#edc11d';
            } else {
                colorTextWave.style.color = 'red';
            }
        } else {
            colorTextWave.style.color = '#93CC56';
        }
    }

    function generateWavePath(offset, waveHeight) {
        return `
            M0 ${waveHeight + Math.sin(offset) * 10}
            Q50 ${waveHeight - 10 + Math.cos(offset) * 10}, 100 ${waveHeight + Math.sin(offset) * 10}
            T200 ${waveHeight + Math.cos(offset) * 10}
            V200 H0 Z
        `;
    }

    function animateWave(waveHeight) {
        let offset = 0;
        function update() {
            offset += 0.05;
            wavePath.setAttribute('d', generateWavePath(offset, waveHeight));
            requestAnimationFrame(update);
        }
        update();
    }

    if (!isNaN(humidity)) {
        const height = Math.min(200, Math.max(0, (1 - humidity / 100) * 150));
        updateHumidityColor();
        wavePath.setAttribute('d', generateWavePath(0, height));
        animateWave(height);
    }

    // Gestion de la température
    const thermometerLevel = document.querySelector('.thermometer-level');
    const colorBulbTemp = document.querySelector('.thermometer-bulb');

    function updateTemperatureColor() {
        if (temp >= 17 && temp <= 21) {
            colorBulbTemp.style.background = '#93CC56';
            thermometerLevel.style.background = '#93CC56';
        } else if ((temp > 21 && temp <= 26) || (temp < 17 && temp >= 12)) {
            colorBulbTemp.style.background = '#edc11d';
            thermometerLevel.style.background = '#edc11d';
        } else {
            colorBulbTemp.style.background = '#b22222';
            thermometerLevel.style.background = '#b22222';
        }
    }

    if (!isNaN(temp)) {
        updateTemperatureColor();
        const maxTemp = 40;
        const minTemp = -10;
        const clampedTemp = Math.max(minTemp, Math.min(maxTemp, temp));
        const height = ((clampedTemp - minTemp) / (maxTemp - minTemp)) * 250;
        thermometerLevel.style.height = `${height}px`;


    }

    // Gestion du CO2
    const gradient = document.getElementById('co2-gradient');
    const colorTextCO2 = document.querySelector('.circle-info-cloud');

    function updateCO2Color() {
        if (co2Value >= 400 && co2Value <= 1000) {
            colorTextCO2.style.color = '#93CC56';
        } else if (co2Value > 1000 && co2Value <= 1400) {
            colorTextCO2.style.color = '#edc11d';
        } else {
            colorTextCO2.style.color = '#b22222';
        }
    }

    if (!isNaN(co2Value)) {
        updateCO2Color();
        const maxCO2 = 1500;
        const minCO2 = 400;
        const clampedCO2 = Math.max(minCO2, Math.min(maxCO2, co2Value));
        const offsetPercentage = (1 - clampedCO2 / maxCO2) * 100;

        const stops = gradient.querySelectorAll('stop');
        stops[0].setAttribute('offset', `${offsetPercentage}%`);
        stops[1].setAttribute('offset', `${offsetPercentage}%`);
    }
});