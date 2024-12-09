class Slider {
  constructor(sliderElement) {
    this.sliderElement = sliderElement;
    this.slideIndex = 1;
    this.slides = this.sliderElement.getElementsByClassName("mySlides");
    this.dots = this.sliderElement.getElementsByClassName("dot");

    // Initialisierung
    this.showSlides(this.slideIndex);

    // Event Listener für "next" und "previous" Buttons
    const nextButton = this.sliderElement.querySelector(".next");
    const prevButton = this.sliderElement.querySelector(".prev");
    const dotButtons = this.sliderElement.querySelectorAll(".dot");

    nextButton?.addEventListener("click", () => this.plusSlides(1));
    prevButton?.addEventListener("click", () => this.plusSlides(-1));

    dotButtons.forEach((dot, index) => {
      dot.addEventListener("click", () => this.currentSlide(index + 1)); // 1-basierte Indexierung
    });
    console.log("new slider", sliderElement);
  }

  plusSlides(n) {
    this.showSlides((this.slideIndex += n));
  }

  currentSlide(n) {
    this.showSlides((this.slideIndex = n));
  }

  showSlides(n) {
    let i;

    if (n > this.slides.length) {
      this.slideIndex = 1;
    }
    if (n < 1) {
      this.slideIndex = this.slides.length;
    }

    for (i = 0; i < this.slides.length; i++) {
      this.slides[i].style.display = "none";
    }

    for (i = 0; i < this.dots.length; i++) {
      this.dots[i].className = this.dots[i].className.replace(" active", "");
    }

    this.slides[this.slideIndex - 1].style.display = "block";
    this.dots[this.slideIndex - 1].className += " active";
  }
}
