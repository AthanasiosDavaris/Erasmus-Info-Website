function toggleMenu() {
  const navbar = document.querySelector('.navbar');
  const hamburger = document.querySelector('.dropdown');
  navbar.classList.toggle('active');
  hamburger.classList.toggle('active');
}