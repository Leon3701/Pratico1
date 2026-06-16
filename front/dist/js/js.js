
document.addEventListener('DOMContentLoaded', function () {
  // 1. Select the carousel element and initialize it explicitly
  const carouselElement = document.getElementById('car');
  const carousel = new bootstrap.Carousel(carouselElement, {
    interval: 3000, // Set your desired speed (3000ms = 3 seconds)
    pause: false    // Disable default hover pause so the button has full control
  });

  // 2. Select the button and icon
  const pauseBtn = document.getElementById('carouselPauseBtn');
  const pauseIcon = document.getElementById('pauseIcon');

  // 3. Add Click Event Listener
  let isPaused = false;

  pauseBtn.addEventListener('click', function () {
    if (isPaused) {
      // Resume
      carousel.cycle();
      pauseIcon.classList.remove('bi-play-fill');
      pauseIcon.classList.add('bi-pause-fill');
      pauseBtn.innerHTML = '<span id="pauseIcon" class="bi bi-pause-fill"></span> Pause';
      isPaused = false;
    } else {
      // Pause
      carousel.pause();
      // Re-select icon because innerHTML replaced it
      const currentIcon = document.getElementById('pauseIcon'); 
      if(currentIcon) {
          currentIcon.classList.remove('bi-pause-fill');
          currentIcon.classList.add('bi-play-fill');
      }
      pauseBtn.innerHTML = '<span id="pauseIcon" class="bi bi-play-fill"></span> Play';
      isPaused = true;
    }
  });
});

/*Auto update year*/
document.getElementById("year").textContent = new Date().getFullYear();


