// Unit conversion data
const units = {
  length: {
    mm: 0.001,
    cm: 0.01,
    m: 1,
    km: 1000,
    in: 0.0254,
    ft: 0.3048,
    yd: 0.9144,
    mi: 1609.344
  },
  weight: {
    mg: 0.000001,
    g: 0.001,
    kg: 1,
    oz: 0.0283495,
    lb: 0.453592,
    ton: 1000
  },
  temperature: {
    C: 'Celsius',
    F: 'Fahrenheit',
    K: 'Kelvin'
  }
};

// Function to update unit options based on conversion type
function updateUnits() {
  const type = document.getElementById('type').value;
  const fromUnit = document.getElementById('fromUnit');
  const toUnit = document.getElementById('toUnit');

  fromUnit.innerHTML = '';
  toUnit.innerHTML = '';

  for (const unit in units[type]) {
    const option1 = document.createElement('option');
    option1.value = unit;
    option1.text = unit;
    fromUnit.appendChild(option1);

    const option2 = document.createElement('option');
    option2.value = unit;
    option2.text = unit;
    toUnit.appendChild(option2);
  }
}

// Function to convert units
function convert() {
  const type = document.getElementById('type').value;
  const value = parseFloat(document.getElementById('value').value);
  const fromUnit = document.getElementById('fromUnit').value;
  const toUnit = document.getElementById('toUnit').value;
  const resultElement = document.getElementById('result');

  if (isNaN(value)) {
    resultElement.textContent = 'Please enter a valid number';
    return;
  }

  let result;

  if (type === 'temperature') {
    result = convertTemperature(value, fromUnit, toUnit);
  } else {
    const fromValue = units[type][fromUnit];
    const toValue = units[type][toUnit];
    result = (value * fromValue) / toValue;
  }

  resultElement.textContent = `${value} ${fromUnit} = ${result.toFixed(4)} ${toUnit}`;
}

// Function to convert temperature
function convertTemperature(value, from, to) {
  let celsius;

  // Convert to Celsius first
  if (from === 'F') {
    celsius = (value - 32) * 5 / 9;
  } else if (from === 'K') {
    celsius = value - 273.15;
  } else {
    celsius = value;
  }

  // Convert from Celsius to target
  if (to === 'F') {
    return (celsius * 9 / 5) + 32;
  } else if (to === 'K') {
    return celsius + 273.15;
  } else {
    return celsius;
  }
}

// Initialize units on page load
document.addEventListener('DOMContentLoaded', updateUnits);

// Update units when type changes
document.getElementById('type').addEventListener('change', updateUnits);