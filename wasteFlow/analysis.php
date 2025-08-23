<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Wastesflow — Comprehensive Analysis</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">

  <!-- Tailwind CSS CDN for modern styling -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Chart.js CDN for dynamic charts and graphs -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!--<style>
    /* Custom styles for a clean, professional look */
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f3f4f6;
      color: #374151;
    }
    .wf-body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }
    .wf-container {
      max-width: 900px;
      width: 95%;
      background-color: #ffffff;
      padding: 2.5rem;
      border-radius: 1.5rem;
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      margin-top: 2rem;
      margin-bottom: 2rem;
    }
    .wf-header {
      text-align: center;
      margin-bottom: 2.5rem;
    }
    .wf-title {
      font-size: 2.5rem;
      font-weight: 800;
      color: #1d4ed8;
      letter-spacing: -0.05em;
    }
    .wf-h2 {
      font-size: 1.5rem;
      font-weight: 600;
      color: #1f2937;
      margin-bottom: 1.5rem;
    }
    .wf-form .wf-field label {
      display: block;
      font-size: 0.875rem;
      font-weight: 500;
      color: #4b5563;
      margin-bottom: 0.5rem;
    }
    .wf-form .wf-field input,
    .wf-form .wf-field select {
      width: 100%;
      padding: 0.75rem 1rem;
      border: 1px solid #d1d5db;
      border-radius: 0.75rem;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .wf-form .wf-field input:focus,
    .wf-form .wf-field select:focus {
      outline: none;
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
    }
    .wf-btn {
      padding: 0.75rem 1.5rem;
      border-radius: 0.75rem;
      font-weight: 600;
      transition: all 0.2s ease-in-out;
      border: 1px solid transparent;
      text-align: center;
      cursor: pointer;
    }
    .wf-btn.primary {
      background-color: #2563eb;
      color: #ffffff;
    }
    .wf-btn.primary:hover {
      background-color: #1e40af;
    }
    .wf-btn.secondary {
      background-color: #f3f4f6;
      color: #4b5563;
      border-color: #d1d5db;
    }
    .wf-btn.secondary:hover {
      background-color: #e5e7eb;
    }
    .wf-table-container {
      overflow-x: auto;
    }
    .wf-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
      font-size: 0.875rem;
    }
    .wf-table th, .wf-table td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid #e5e7eb;
    }
    .wf-table th {
      background-color: #f9fafb;
      font-weight: 600;
      color: #4b5563;
    }
    .wf-table tbody tr:last-child td {
      border-bottom: none;
    }
    .wf-message {
      padding: 1rem;
      border-radius: 0.75rem;
      margin-bottom: 1.5rem;
      font-weight: 500;
      text-align: center;
    }
    .wf-message.success {
      background-color: #d1fae5;
      color: #065f46;
    }
    .wf-message.error {
      background-color: #fee2e2;
      color: #991b1b;
    }
  </style>-->
</head>
<body class="wf-body">

  <!-- Main container for the entire application -->
  <div class="wf-container">

    <!-- Header section -->
    <header class="wf-header">
      <div class="wf-title">Wastesflow</div>
      <p class="text-sm text-gray-500">Daily Waste Analysis</p>
    </header>

    <!-- Input Form Page -->
    <section id="input-page">
      <h2 class="wf-h2">Enter Daily Waste Data</h2>
      <div id="status-message" class="wf-message hidden"></div>
      <form id="waste-form" class="wf-form">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div class="wf-field">
            <label for="collection-date">Collection Date</label>
            <input type="date" id="collection-date" name="collection-date" required>
          </div>
          <div class="wf-field">
            <label for="waste-type">Waste Type</label>
            <select id="waste-type" name="waste-type" required>
              <option value="">Select Waste Type</option>
              <option value="Organic">Organic</option>
              <option value="Plastic">Plastic</option>
              <option value="E-Waste">E-Waste</option>
              <option value="Medical">Medical</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="wf-field">
            <label for="quantity">Quantity (kg)</label>
            <input type="number" id="quantity" name="quantity" placeholder="e.g. 15.5" min="0" step="0.1" required>
          </div>
        </div>
        <div class="flex flex-col sm:flex-row gap-4 mt-6">
          <button type="submit" class="wf-btn primary w-full">Submit Data</button>
          <button type="button" id="show-analysis-btn" class="wf-btn secondary w-full">View Analysis</button>
        </div>
      </form>
    </section>

    <!-- Analysis Output Page (Hidden by default) -->
    <section id="analysis-page" class="hidden">
      <h2 class="wf-h2">Waste Analysis Report</h2>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Daily Total Chart (Bar) -->
        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
          <h3 class="text-lg font-semibold mb-4 text-gray-700">Daily Totals (kg)</h3>
          <canvas id="daily-total-chart" height="180"></canvas>
        </div>

        <!-- Waste Type Distribution Chart (Pie) -->
        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
          <h3 class="text-lg font-semibold mb-4 text-gray-700">Type Distribution</h3>
          <canvas id="type-distribution-chart" height="180"></canvas>
        </div>

        <!-- Total Waste Trend Chart (Line) -->
        <div class="lg:col-span-2 bg-gray-50 p-6 rounded-xl border border-gray-200">
          <h3 class="text-lg font-semibold mb-4 text-gray-700">Waste Trend Over Time</h3>
          <canvas id="waste-trend-chart" height="150"></canvas>
        </div>
      </div>

      <!-- Data tables -->
      <h3 class="text-lg font-semibold mt-8 mb-4 text-gray-700">Data Tables</h3>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Summary Table -->
        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
          <h4 class="text-md font-semibold mb-4 text-gray-700">Total by Waste Type</h4>
          <div class="wf-table-container">
            <table id="summary-table" class="wf-table">
              <thead>
                <tr>
                  <th>Waste Type</th>
                  <th>Total (kg)</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>

        <!-- Raw Data Table -->
        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
          <h4 class="text-md font-semibold mb-4 text-gray-700">Raw Data Entries</h4>
          <div class="wf-table-container">
            <table id="data-table" class="wf-table">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Type</th>
                  <th>Quantity (kg)</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="flex justify-center mt-6">
        <button type="button" id="back-to-input-btn" class="wf-btn secondary">Back to Input</button>
      </div>
    </section>
  </div>

  <script>
    // Get references to the main HTML elements
    const inputPage = document.getElementById('input-page');
    const analysisPage = document.getElementById('analysis-page');
    const wasteForm = document.getElementById('waste-form');
    const showAnalysisBtn = document.getElementById('show-analysis-btn');
    const backToInputBtn = document.getElementById('back-to-input-btn');
    const statusMessage = document.getElementById('status-message');
    const dataTableBody = document.querySelector('#data-table tbody');
    const summaryTableBody = document.querySelector('#summary-table tbody');

    // Chart instances to prevent duplicates
    let dailyTotalChart, typeDistributionChart, wasteTrendChart;

    // Data stored in the browser's local storage
    let wasteData = JSON.parse(localStorage.getItem('wastesflowData')) || [];

    // Helper function to show a page
    const showPage = (pageId) => {
      document.querySelectorAll('section').forEach(page => page.classList.add('hidden'));
      document.getElementById(pageId).classList.remove('hidden');
    };

    // Helper function to display a temporary message
    const showMessage = (message, type = 'success') => {
      statusMessage.textContent = message;
      statusMessage.classList.remove('hidden', 'success', 'error');
      statusMessage.classList.add(type);
      setTimeout(() => statusMessage.classList.add('hidden'), 5000);
    };

    // Function to render all charts and tables
    const renderAnalysis = () => {
      // Destroy any existing chart instances
      if (dailyTotalChart) dailyTotalChart.destroy();
      if (typeDistributionChart) typeDistributionChart.destroy();
      if (wasteTrendChart) wasteTrendChart.destroy();

      // Aggregate data in a single pass
      const dailyTotals = {};
      const typeTotals = {};
      
      wasteData.forEach(entry => {
        dailyTotals[entry.date] = (dailyTotals[entry.date] || 0) + entry.quantity;
        typeTotals[entry.type] = (typeTotals[entry.type] || 0) + entry.quantity;
      });

      // Sort dates for chronological charts
      const sortedDates = Object.keys(dailyTotals).sort();

      // BAR CHART: Daily Totals
      dailyTotalChart = new Chart(document.getElementById('daily-total-chart').getContext('2d'), {
        type: 'bar',
        data: {
          labels: sortedDates,
          datasets: [{
            label: 'Total Waste (kg)',
            data: sortedDates.map(date => dailyTotals[date]),
            backgroundColor: '#2563eb',
            borderRadius: 8
          }]
        }
      });

      // PIE CHART: Type Distribution
      const typeLabels = Object.keys(typeTotals);
      typeDistributionChart = new Chart(document.getElementById('type-distribution-chart').getContext('2d'), {
        type: 'pie',
        data: {
          labels: typeLabels,
          datasets: [{
            data: typeLabels.map(label => typeTotals[label]),
            backgroundColor: ['#28a745', '#ffc107', '#6f42c1', '#dc3545', '#17a2b8'],
            borderColor: '#ffffff',
            borderWidth: 2
          }]
        }
      });
      
      // LINE CHART: Waste Trend Over Time
      const cumulativeTotals = sortedDates.reduce((acc, date, index) => {
          const dailyTotal = dailyTotals[date];
          const previousTotal = index > 0 ? acc[index - 1] : 0;
          acc.push(previousTotal + dailyTotal);
          return acc;
      }, []);

      wasteTrendChart = new Chart(document.getElementById('waste-trend-chart').getContext('2d'), {
          type: 'line',
          data: {
              labels: sortedDates,
              datasets: [{
                  label: 'Cumulative Waste (kg)',
                  data: cumulativeTotals,
                  borderColor: '#ef4444',
                  backgroundColor: 'rgba(239, 68, 68, 0.2)',
                  tension: 0.4,
                  fill: true,
                  pointRadius: 5,
                  pointBackgroundColor: '#dc2626'
              }]
          },
          options: {
              scales: { y: { beginAtZero: true } }
          }
      });

      // SUMMARY TABLE: Total by Waste Type
      summaryTableBody.innerHTML = '';
      typeLabels.forEach(type => {
        const row = summaryTableBody.insertRow();
        row.innerHTML = `
          <td class="whitespace-nowrap">${type}</td>
          <td class="whitespace-nowrap">${typeTotals[type].toFixed(2)} kg</td>
        `;
      });

      // RAW DATA TABLE: All entries
      dataTableBody.innerHTML = '';
      wasteData.sort((a, b) => new Date(b.date) - new Date(a.date));
      wasteData.forEach(entry => {
        const row = dataTableBody.insertRow();
        row.innerHTML = `
          <td class="whitespace-nowrap">${entry.date}</td>
          <td class="whitespace-nowrap capitalize">${entry.type}</td>
          <td class="whitespace-nowrap">${entry.quantity.toFixed(2)} kg</td>
        `;
      });
    };

    // Event listener for form submission
    wasteForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const newEntry = {
        date: wasteForm.elements['collection-date'].value,
        type: wasteForm.elements['waste-type'].value,
        quantity: parseFloat(wasteForm.elements['quantity'].value)
      };
      
      if (isNaN(newEntry.quantity) || newEntry.quantity < 0) {
        showMessage('Please enter a valid, positive number for quantity.', 'error');
        return;
      }

      wasteData.push(newEntry);
      localStorage.setItem('wastesflowData', JSON.stringify(wasteData));
      
      showMessage('Data submitted successfully!');
      wasteForm.reset();
    });

    // Event listeners to navigate between pages
    showAnalysisBtn.addEventListener('click', () => {
      renderAnalysis();
      showPage('analysis-page');
    });

    backToInputBtn.addEventListener('click', () => {
      showPage('input-page');
    });

    // Initial page load
    showPage('input-page');
  </script>

</body>
</html>
