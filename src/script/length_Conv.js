const typeSelect = document.getElementById("type");
const fromUnit = document.getElementById("fromUnit");
const toUnit = document.getElementById("toUnit");
const result = document.getElementById("result");

const units = {
  length: ["Meter", "Kilometer", "Centimeter"],
  weight: ["Kilogram", "Gram", "Pound"],
  temperature: ["Celsius", "Fahrenheit", "Kelvin"]
};

// Load units on page load
updateUnits();

// Change units when type changes
typeSelect.addEventListener("change", updateUnits);

function updateUnits() {
  const type = typeSelect.value;
  fromUnit.innerHTML = "";
  toUnit.innerHTML = "";

  units[type].forEach(unit => {
    fromUnit.innerHTML += `<option value="${unit}">${unit}</option>`;
    toUnit.innerHTML += `<option value="${unit}">${unit}</option>`;
  });
}

function convert() {
  const value = parseFloat(document.getElementById("value").value);
  const type = typeSelect.value;
  const from = fromUnit.value;
  const to = toUnit.value;

  if (isNaN(value)) {
    result.innerText = "Please enter a valid number";
    return;
  }

  let convertedValue;

  if (type === "length") {
    convertedValue = convertLength(value, from, to);
  } else if (type === "weight") {
    convertedValue = convertWeight(value, from, to);
  } else {
    convertedValue = convertTemperature(value, from, to);
  }

  result.innerText = `${value} ${from} = ${convertedValue.toFixed(2)} ${to}`;
}

// Length Conversion
function convertLength(value, from, to) {
  let meter;

  if (from === "Kilometer") meter = value * 1000;
  else if (from === "Centimeter") meter = value / 100;
  else meter = value;

  if (to === "Kilometer") return meter / 1000;
  if (to === "Centimeter") return meter * 100;
  return meter;
}

// Weight Conversion
function convertWeight(value, from, to) {
  let kg;

  if (from === "Gram") kg = value / 1000;
  else if (from === "Pound") kg = value * 0.453592;
  else kg = value;

  if (to === "Gram") return kg * 1000;
  if (to === "Pound") return kg / 0.453592;
  return kg;
}

// Temperature Conversion
function convertTemperature(value, from, to) {
  if (from === to) return value;

  if (from === "Celsius") {
    if (to === "Fahrenheit") return (value * 9/5) + 32;
    if (to === "Kelvin") return value + 273.15;
  }

  if (from === "Fahrenheit") {
    if (to === "Celsius") return (value - 32) * 5/9;
    if (to === "Kelvin") return (value - 32) * 5/9 + 273.15;
  }

  if (from === "Kelvin") {
    if (to === "Celsius") return value - 273.15;
    if (to === "Fahrenheit") return (value - 273.15) * 9/5 + 32;
  }
}
