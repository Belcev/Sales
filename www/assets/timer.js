document.addEventListener('DOMContentLoaded', function() {
	// Najdeme všechny elementy s třídou 'countdown'
	const countdownElements = document.querySelectorAll('.countdown');

	countdownElements.forEach(function(countdownElement) {
		// Získáme cílové datum z atributu data-date
		const targetDate = new Date(countdownElement.getAttribute('data-timeTo')).getTime();

		// Funkce pro aktualizaci odpočítávadla
		function updateCountdown() {
			const now = new Date().getTime();
			const distance = targetDate - now;

			// Výpočet dnů, hodin, minut a sekund
			const days = Math.floor(distance / (1000 * 60 * 60 * 24));
			const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			const seconds = Math.floor((distance % (1000 * 60)) / 1000);

			// Zobrazení výsledku v elementu
			countdownElement.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;

			// Pokud je odpočítávání u konce, zobrazíme zprávu
			if (distance < 0) {
				clearInterval(interval);
				// refrešnout stránku
				location.reload();
			}
		}

		// Aktualizujeme odpočítávadlo každou sekundu
		const interval = setInterval(updateCountdown, 1000);

		// Okamžitá aktualizace pro zobrazení počátečního stavu
		updateCountdown();
	});
});