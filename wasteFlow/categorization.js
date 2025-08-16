let wasteId = 1;

document.getElementById("wasteForm").addEventListener("submit", function(e) {
  e.preventDefault();

  let type = document.getElementById("wasteType").value;
  let qty = document.getElementById("quantity").value;
  let area = document.getElementById("area").value;
  let date = document.getElementById("date").value;

  let table = document.getElementById("wasteTable").getElementsByTagName("tbody")[0];
  let newRow = table.insertRow();

  newRow.innerHTML = `
    <td>${wasteId++}</td>
    <td>${type}</td>
    <td>${qty}</td>
    <td>${area}</td>
    <td>${date}</td>
    <td>Categorized</td>
  `;

  document.getElementById("wasteForm").reset();
});

function analyzeData() {
  document.getElementById("reportOutput").innerHTML = "<b>Trend Analysis:</b> Organic waste is highest in Mirpur, E-waste increasing in Gulshan.";
}

function generateReport() {
  document.getElementById("reportOutput").innerHTML = "<b>Report Generated:</b> Waste data stored and categorized for city planning.";
}

function shareFindings() {
  document.getElementById("reportOutput").innerHTML = "<b>Findings Shared:</b> Insights sent to City Authority and NGOs.";
}
