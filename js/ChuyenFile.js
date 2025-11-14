
document.querySelectorAll('a[href]').forEach(link => {
  const href = link.getAttribute('href');
  if (href && href.endsWith('.php') && !href.includes('mailto') && !href.includes('tel')) {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      document.body.classList.add('fade-out');
      setTimeout(() => {
        window.location = href;
      }, 400);
    });
  }
});
