// Simulated Database
let bins = [];

// Function to generate bin status cards
function renderBins() {
    const container = document.getElementById('bin-cards-container');
    container.innerHTML = ''; // clear previous

    bins.forEach(bin => {
        const card = document.createElement('div');
        card.className = 'bin-card';
        card.innerHTML = `
            <h3>Bin ${bin.id}</h3>
            <p><strong>Location:</strong> ${bin.location}</p>
            <p><strong>Fill Level:</strong> <span class="${getLevelClass(bin.level)}">${bin.level}%</span></p>
            <p><strong>Status:</strong> ${getLevelText(bin.level)}</p>
        `;
        container.appendChild(card);
    });
}

// Function to get bin level text
function getLevelText(level) {
    if (level < 50) return 'Low';
    if (level < 80) return 'Medium';
    return 'Full';
}

// Function to get bin level class for styling
function getLevelClass(level) {
    if (level < 50) return 'low';
    if (level < 80) return 'medium';
    return 'full';
}

// Function to trigger alerts
function triggerAlerts() {
    const alertsContainer = document.getElementById('alerts-list');
    alertsContainer.innerHTML = '';
    bins.forEach(bin => {
        if (bin.level >= 80) {
            const li = document.createElement('li');
            li.textContent = `⚠️ Bin ${bin.id} at ${bin.location} is ${getLevelText(bin.level)} (${bin.level}%)`;
            alertsContainer.appendChild(li);
        }
    });
}

// Handle form submission
document.getElementById('bin-form').addEventListener('submit', function(e){
    e.preventDefault();
    const id = document.getElementById('bin-id').value;
    const location = document.getElementById('bin-location').value;
    const level = parseInt(document.getElementById('bin-level').value);

    // Check if bin exists
    const existingIndex = bins.findIndex(b => b.id === id);
    if(existingIndex !== -1){
        bins[existingIndex] = {id, location, level}; // update
    } else {
        bins.push({id, location, level}); // add new
    }

    renderBins();
    triggerAlerts();

    // Clear form
    this.reset();
});

// Initial render
renderBins();
triggerAlerts();
