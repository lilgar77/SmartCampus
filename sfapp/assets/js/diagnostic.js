

// ---- Analyse des données ----
const analyzeCO2 = (co2Data) => {
    const thresholds = { green: 0, yellow: 0, red: 0 };

    co2Data.forEach(value => {
        if (value >= 400 && value < 1000) thresholds.green++;
        else if (value >= 1000 && value < 1400) thresholds.yellow++;
        else thresholds.red++;
    });

    return thresholds;
};

const analyzeTemp = (tempData) => {
    const thresholds = {
        darkBlue: 0, lightBlue: 0, green: 0,
        yellow: 0, orange: 0, red: 0
    };

    tempData.forEach(value => {
        if (value < 10.0) thresholds.darkBlue++;
        else if (value >= 10.0 && value < 17.0) thresholds.lightBlue++;
        else if (value >= 17.0 && value <= 21.0) thresholds.green++;
        else if (value > 21.0 && value < 25.0) thresholds.yellow++;
        else if (value >= 25.0 && value < 35.0) thresholds.orange++;
        else thresholds.red++;
    });

    return thresholds;
};

const analyzeHumidity = (humData, tempData) => {
    const thresholds = { green: 0, yellow: 0, red: 0 };

    humData.forEach((value, index) => {
        if (value >= 70.0 && tempData[index] >= 25) thresholds.red++;
        else if (value >= 70.0 && (tempData[index] >= 20 && tempData[index] < 25)) thresholds.yellow++;
        else thresholds.green++;
    });

    return thresholds;
};

// ---- Définir les seuils ----
const co2Thresholds = analyzeCO2(co2Data);
const tempThresholds = analyzeTemp(tempData);
const humThresholds = analyzeHumidity(humData, tempData);

// ---- Afficher les analyses ----
const updateAnalysisText = (thresholds, elementId, descriptions) => {
    const maxThreshold = Math.max(...Object.values(thresholds));
    const key = Object.keys(thresholds).find(k => thresholds[k] === maxThreshold);
    document.getElementById(elementId).textContent = descriptions[key] || "Impossible de déterminer le seuil.";
};

// Texte d'analyse pour chaque type de données
const co2Descriptions = {
    green: "Le niveau de CO2 est dans le seuil vert (faible). 🌱 La qualité de l'air est bonne.",
    yellow: "Le niveau de CO2 est dans le seuil jaune (modéré). ⚠️ Cela indique que la qualité de l'air commence à se dégrader.",
    red: "Le niveau de CO2 est dans le seuil rouge (élevé). 🚨 La qualité de l'air est mauvaise."
};

const tempDescriptions = {
    darkBlue: "La température est très basse (seuil bleu foncé). ❄️ Il fait anormalement froid.",
    lightBlue: "La température est basse (seuil bleu clair). 🌬️ La pièce est fraîche.",
    green: "La température est dans un seuil idéal (seuil vert). 🌡️ Les conditions sont optimales.",
    yellow: "La température est modérée (seuil jaune). ⚠️ Elle commence à être légèrement élevée.",
    orange: "La température est élevée (seuil orange). 🟠 La pièce est chaude.",
    red: "La température est critique (seuil rouge). 🚨 Il fait trop chaud."
};

const humDescriptions = {
    green: "Le niveau d'humidité est idéal (seuil vert). 💧 L'air est confortable et équilibré.",
    yellow: "Le niveau d'humidité est modéré (seuil jaune). ⚠️ L'air est légèrement trop sec ou trop humide.",
    red: "Le niveau d'humidité est critique (seuil rouge). 🚨 Un excès ou un manque important d'humidité est détecté."
};

updateAnalysisText(co2Thresholds, 'analyse-text-co2', co2Descriptions);
updateAnalysisText(tempThresholds, 'analyse-text-temp', tempDescriptions);
updateAnalysisText(humThresholds, 'analyse-text-hum', humDescriptions);

// ---- Générer les graphiques ----
const createChart = (ctx, labels, data, colors) => {
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: colors,
                hoverOffset: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: { enabled: true }
            }
        }
    });
};

// Données pour les graphiques
createChart(
    document.getElementById('co2Chart').getContext('2d'),
    ['400-1000 ppm', '1000-1400 ppm', '>1400 ppm'],
    [co2Thresholds.green, co2Thresholds.yellow, co2Thresholds.red],
    ['rgb(41,112,6)', 'rgb(184,177,2)', 'rgb(255,0,55)']
);

createChart(
    document.getElementById('tempChart').getContext('2d'),
    ['<10°C', '10-17°C', '17-21°C', '21-25°C', '25-35°C', '>35°C'],
    [
        tempThresholds.darkBlue, tempThresholds.lightBlue,
        tempThresholds.green, tempThresholds.yellow,
        tempThresholds.orange, tempThresholds.red
    ],
    ['rgb(6,20,112)', 'rgb(8,138,209)', 'rgb(41,112,6)', 'rgb(184,177,2)', 'rgb(209,92,8)', 'rgb(255,0,55)']
);

createChart(
    document.getElementById('humChart').getContext('2d'),
    ['Critique', 'Modéré', 'Idéal'],
    [humThresholds.red, humThresholds.yellow, humThresholds.green],
    ['rgb(255,0,55)', 'rgb(184,177,2)', 'rgb(41,112,6)']
);