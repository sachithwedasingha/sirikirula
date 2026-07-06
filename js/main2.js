/* ============================================================
   Siri Kirula - Main JavaScript / jQuery
   ============================================================ */

$(document).ready(function () {

  /* ---- PRELOADER ---- */
  setTimeout(function () {
    $('#preloader').fadeOut(600);
  }, 1200);

  /* ---- SCROLL TOP BUTTON ---- */
  $(window).on('scroll', function () {
    if ($(this).scrollTop() > 300) {
      $('#scrollTop').addClass('show');
    } else {
      $('#scrollTop').removeClass('show');
    }
  });
  $('#scrollTop').on('click', function () {
    $('html, body').animate({ scrollTop: 0 }, 600, 'swing');
  });

  /* ---- SCROLL REVEAL ---- */
  function checkVisibility() {
    var windowBottom = $(window).scrollTop() + $(window).height();
    $('.fade-in-up, .fade-in-left, .fade-in-right').each(function () {
      var elemTop = $(this).offset().top;
      if (windowBottom > elemTop + 60) {
        var delay = $(this).data('delay') || 0;
        var $el = $(this);
        setTimeout(function () { $el.addClass('visible'); }, delay);
      }
    });
  }
  checkVisibility();
  $(window).on('scroll', checkVisibility);

  /* ---- HERO PARTICLES ---- */
  function createParticles() {
    var container = $('.hero-particles');
    if (!container.length) return;
    for (var i = 0; i < 20; i++) {
      (function (i) {
        var left  = Math.random() * 100;
        var delay = Math.random() * 6;
        var dur   = 4 + Math.random() * 5;
        var size  = 3 + Math.random() * 5;
        var p = $('<div class="particle"></div>').css({
          left: left + '%',
          width: size + 'px', height: size + 'px',
          animationDelay: delay + 's',
          animationDuration: dur + 's'
        });
        container.append(p);
      })(i);
    }
  }
  createParticles();

  /* ---- COUNTER ANIMATION ---- */
  function animateCounters() {
    $('.stat-num[data-target]').each(function () {
      var $el = $(this);
      var target = parseInt($el.data('target'));
      var suffix = $el.data('suffix') || '';
      $({ count: 0 }).animate({ count: target }, {
        duration: 2000,
        easing: 'swing',
        step: function () { $el.text(Math.floor(this.count) + suffix); },
        complete: function () { $el.text(target + suffix); }
      });
    });
  }
  // Trigger when stats come into view
  var countersDone = false;
  $(window).on('scroll', function () {
    if (countersDone) return;
    var $stats = $('.hero-stats');
    if (!$stats.length) return;
    var top = $stats.offset().top;
    if ($(this).scrollTop() + $(this).height() > top) {
      animateCounters();
      countersDone = true;
    }
  });
  animateCounters(); // run on load too

  /* ---- NAVBAR ACTIVE STATE ---- */
  var page = window.location.pathname.split('/').pop() || 'index.html';
  $('.nav-link-custom').each(function () {
    var href = $(this).attr('href');
    if (href === page) $(this).addClass('active');
  });

  /* ---- PRODUCT FILTER ---- */
  $(document).on('click', '.filter-btn', function () {
    $('.filter-btn').removeClass('active');
    $(this).addClass('active');
    var filter = $(this).data('filter');
    if (filter === 'all') {
      $('.product-card-wrap').fadeIn(300);
    } else {
      $('.product-card-wrap').hide();
      $('.product-card-wrap[data-category="' + filter + '"]').fadeIn(300);
    }
  });

  /* ---- SEARCH ---- */
  $(document).on('keyup', '#searchInput', function () {
    var val = $(this).val().toLowerCase();
    $('.product-card-wrap').each(function () {
      var name = $(this).find('.product-name').text().toLowerCase();
      var cat  = $(this).find('.product-category').text().toLowerCase();
      if (name.includes(val) || cat.includes(val)) {
        $(this).fadeIn(200);
      } else {
        $(this).fadeOut(200);
      }
    });
  });

  /* ---- PRODUCT QUICK VIEW MODAL ---- */
  $(document).on('click', '.btn-quickview', function () {
    var card    = $(this).closest('.product-card');
    var name    = card.find('.product-name').text();
    var price   = card.find('.price').text();
    var desc    = card.find('.product-desc').text();
    var imgSrc  = card.find('.product-img-wrap img').attr('src');
    var cat     = card.find('.product-category').text();

    $('#qvModalTitle').text(name);
    $('#qvModalImg').attr('src', imgSrc).attr('alt', name);
    $('#qvModalName').text(name);
    $('#qvModalCat').text(cat);
    $('#qvModalDesc').text(desc);
    $('#qvModalPrice').text(price);
    $('#quickViewModal').modal('show');
  });

  /* ---- WISHLIST TOGGLE ---- */
  $(document).on('click', '.btn-wishlist', function () {
    var icon = $(this).find('i');
    if (icon.hasClass('far')) {
      icon.removeClass('far').addClass('fas').css('color', '#D4A017');
      showToast('Added to Wishlist!', 'gold');
    } else {
      icon.removeClass('fas').addClass('far').css('color', '');
      showToast('Removed from Wishlist', 'info');
    }
  });

  /* ---- ADD TO CART ---- */
  $(document).on('click', '.btn-addcart', function () {
    var btn = $(this);
    btn.html('<i class="fas fa-check"></i> Added!').addClass('added');
    setTimeout(function () {
      btn.html('<i class="fas fa-shopping-bag"></i> Add to Cart').removeClass('added');
    }, 2000);
    showToast('Item added to cart!', 'success');
    updateCartCount();
  });

  /* ---- CART COUNT ---- */
  var cartCount = 0;
  function updateCartCount() {
    cartCount++;
    var $badge = $('#cartBadge');
    if (!$badge.length) {
      $('.nav-cta').first().append('<span id="cartBadge" class="cart-badge">' + cartCount + '</span>');
    } else {
      $badge.text(cartCount).addClass('bump');
      setTimeout(function () { $badge.removeClass('bump'); }, 400);
    }
  }

  /* ---- TOAST NOTIFICATION ---- */
  function showToast(msg, type) {
    var colors = {
      gold:    { bg: '#D4A017', color: '#fff' },
      success: { bg: '#1a7a1a', color: '#fff' },
      info:    { bg: '#800000', color: '#FFD700' }
    };
    var c = colors[type] || colors.info;
    var toast = $('<div class="rg-toast"></div>')
      .text(msg)
      .css({ background: c.bg, color: c.color });
    $('body').append(toast);
    setTimeout(function () { toast.addClass('show'); }, 50);
    setTimeout(function () { toast.removeClass('show'); setTimeout(function () { toast.remove(); }, 400); }, 2800);
  }

  /* ---- GALLERY LIGHTBOX ---- */
  $(document).on('click', '.gallery-item', function () {
    var src   = $(this).find('img').attr('src');
    var title = $(this).find('.gallery-overlay span').text();
    $('#lightboxImg').attr('src', src).attr('alt', title);
    $('#lightboxTitle').text(title);
    $('#lightboxModal').modal('show');
  });

  /* ---- CONTACT FORM ---- */
  $(document).on('submit', '#contactForm', function (e) {
    e.preventDefault();
    var btn = $(this).find('[type=submit]');
    btn.html('<i class="fas fa-spinner fa-spin"></i> Sending...').prop('disabled', true);
    setTimeout(function () {
      btn.html('<i class="fas fa-check"></i> Message Sent!').css('background', '#1a7a1a');
      showToast('Your message has been sent! We will contact you soon.', 'success');
      setTimeout(function () {
        btn.html('Send Message').css('background', '').prop('disabled', false);
        $('#contactForm')[0].reset();
      }, 3000);
    }, 1800);
  });

  /* ---- RENT INQUIRY FORM ---- */
  $(document).on('submit', '#rentForm', function (e) {
    e.preventDefault();
    showToast('Rental inquiry submitted! We will call you within 2 hours.', 'gold');
    $(this)[0].reset();
  });

  /* ---- SMOOTH SCROLL FOR ANCHOR LINKS ---- */
  $(document).on('click', 'a[href^="#"]', function (e) {
    var target = $($(this).attr('href'));
    if (target.length) {
      e.preventDefault();
      $('html, body').animate({ scrollTop: target.offset().top - 80 }, 600);
    }
  });

  /* ---- NAVBAR COLLAPSE ON MOBILE LINK CLICK ---- */
  $('.nav-link-custom').on('click', function () {
    if ($('.navbar-collapse').hasClass('show')) {
      $('.navbar-toggler').click();
    }
  });

});

/* ---- DYNAMIC STYLES FOR CART BADGE & TOAST ---- */
$('<style>\
  .cart-badge {\
    position:absolute; top:-8px; right:-8px;\
    background:#FFD700; color:#5a0000;\
    width:20px; height:20px; border-radius:50%;\
    font-size:0.72rem; font-weight:700;\
    display:flex; align-items:center; justify-content:center;\
    transition:transform 0.2s;\
  }\
  .cart-badge.bump { transform:scale(1.4); }\
  .rg-toast {\
    position:fixed; bottom:80px; right:28px; z-index:9999;\
    padding:12px 22px; border-radius:10px;\
    font-size:0.88rem; font-weight:600;\
    box-shadow:0 6px 20px rgba(0,0,0,0.25);\
    transform:translateX(120%); transition:transform 0.4s ease;\
    max-width:300px;\
  }\
  .rg-toast.show { transform:translateX(0); }\
  .btn-addcart.added { background:linear-gradient(135deg,#1a7a1a,#2da02d) !important; }\
  .nav-cta { position:relative; }\
</style>').appendTo('head');
