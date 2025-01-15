document.addEventListener('DOMContentLoaded', () => {
        const progressBar = window.progressbar;
        const carousel = window.carousel;
        const prevButton = window.prevButton;
        const nextButton = window.nextButton;
        const stepsCount = 4;

        carousel.addEventListener('slide.bs.carousel', function (event) {
            const currentIndex = event.to;
            const progress = ((currentIndex + 1) / stepsCount) * 100;

            progressBar.style.width = progress + '%';
            progressBar.textContent = `Étape ${currentIndex + 1} sur ${stepsCount}`;

            prevButton.disabled = currentIndex === 0;
            if (currentIndex === stepsCount - 1) {
                nextButton.textContent = 'Valider l\'installation';
                nextButton.type = 'submit';
                nextButton.removeAttribute('data-bs-slide');
            } else {
                nextButton.textContent = 'Suivant';
                nextButton.type = 'button';
                nextButton.setAttribute('data-bs-slide', 'next');
            }
        });
});