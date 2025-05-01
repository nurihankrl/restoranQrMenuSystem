<div class="col-12 mt-0 footer-sht">
    <!-- footer sticky bottom -->
    <footer class="footer-sticky">
        <ul class="nav nav-pills nav-justified">

            <li class="nav-item centerbutton">
                <a class="nav-link active" href="/">
                    <span class="bg-radial-gradient-theme">
                        <i class="nav-icon bi bi-house"></i>
                    </span>
                </a>
            </li>

        </ul>
    </footer>
    <!-- footer sticky bottom ends -->
</div>

<!-- Required jquery and libraries -->
<script src="assets/js/jquery-3.3.1.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/vendor/bootstrap-5/dist/js/bootstrap.bundle.js"></script>

<!-- Customized jquery file  -->
<script src="assets/js/main.js"></script>
<script src="assets/js/color-scheme.js"></script>

<!-- PWA app service registration and works -->
<script src="assets/js/pwa-services.js"></script>

<!-- date range picker -->
<script src="momentjs/latest/moment.min.js"></script>
<script src="assets/vendor/daterangepicker/daterangepicker.js"></script>

<!-- chosen script -->
<script src="assets/vendor/chosen_v1.8.7/chosen.jquery.min.js"></script>

<!-- Chart js script -->
<script src="assets/vendor/chart-js-3.3.1/chart.min.js"></script>

<!-- no ui slider js script -->
<script src="assets/vendor/nouislider/nouislider.min.js"></script>

<!-- Progress circle js script -->
<script src="assets/vendor/progressbar-js/progressbar.min.js"></script>

<!-- swiper js script -->
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

<!-- page level script here -->
<script>
    'use strict'
    $(window).on('load', function() {
        /* toast action events messages */
        $('#toastprouctaddedtinybtn').on('click', function() {
            $('#toastprouctaddedtiny').toast('show');
        })
        $('#toastprouctaddedbtn').on('click', function() {
            $('#toastprouctadded').toast('show');
        })
        $('#toastprouctaddedrichbtn').on('click', function() {
            $('#toastprouctaddedrich').toast('show');
        })
        /* swiper */
        var swiper = new Swiper(".categories", {
            slidesPerView: "auto",
            spaceBetween: 5,
        });
        var swiper = new Swiper(".popular-categories", {
            slidesPerView: "auto",
            spaceBetween: 30,
        });
        /* circular progress */
        var progressCirclesgreen1 = new ProgressBar.Circle(circleprogressgreen1, {
            color: '#000000',
            // This has to be the same size as the maximum width to
            // prevent clipping
            strokeWidth: 10,
            trailWidth: 10,
            easing: 'easeInOut',
            trailColor: 'rgba(0, 220, 190, 0.15)',
            duration: 1400,
            text: {
                autoStyleContainer: false
            },
            from: {
                color: '#00DCBE',
                width: 10
            },
            to: {
                color: '#00DCBE',
                width: 10
            },
            // Set default step function for all animate calls
            step: function(state, circle) {
                circle.path.setAttribute('stroke', state.color);
                circle.path.setAttribute('stroke-width', state.width);
                var value = Math.round(circle.value() * 100);
                if (value === 0) {
                    circle.setText('');
                } else {
                    circle.setText(value + "<small>%<small>");
                }
            }
        });
        // progressCirclesblue1.text.style.fontSize = '20px';
        progressCirclesgreen1.animate(0.65); // Number from 0.0 to 1.0
        var progressCirclesred1 = new ProgressBar.Circle(circleprogressred1, {
            color: '#000000',
            // This has to be the same size as the maximum width to
            // prevent clipping
            strokeWidth: 10,
            trailWidth: 10,
            easing: 'easeInOut',
            trailColor: 'rgba(255, 9, 109, 0.15)',
            duration: 1400,
            text: {
                autoStyleContainer: false
            },
            from: {
                color: '#FF096D',
                width: 10
            },
            to: {
                color: '#FF096D',
                width: 10
            },
            // Set default step function for all animate calls
            step: function(state, circle) {
                circle.path.setAttribute('stroke', state.color);
                circle.path.setAttribute('stroke-width', state.width);
                var value = Math.round(circle.value() * 100);
                if (value === 0) {
                    circle.setText('');
                } else {
                    circle.setText("<p class='line-height-14px'>" + value +
                        "%<br><small class='text-secondary fs-10'>grams</small></p>");
                }
            }
        });
        // progressCirclesblue1.text.style.fontSize = '20px';
        progressCirclesred1.animate(0.45); // Number from 0.0 to 1.0
        var progressCirclesyellow1 = new ProgressBar.Circle(circleprogressyellow1, {
            color: '#000000',
            // This has to be the same size as the maximum width to
            // prevent clipping
            strokeWidth: 10,
            trailWidth: 10,
            easing: 'easeInOut',
            trailColor: 'rgba(255, 187, 0, 0.15)',
            duration: 1400,
            text: {
                autoStyleContainer: false
            },
            from: {
                color: '#FFBB00',
                width: 10
            },
            to: {
                color: '#FFBB00',
                width: 10
            },
            // Set default step function for all animate calls
            step: function(state, circle) {
                circle.path.setAttribute('stroke', state.color);
                circle.path.setAttribute('stroke-width', state.width);
                var value = Math.round(circle.value() * 100);
                if (value === 0) {
                    circle.setText('');
                } else {
                    circle.setText("<p class='line-height-14px'>" + value +
                        "%<br><small class='text-secondary fs-10'>of DV</small></p>");
                }
            }
        });
        // progressCirclesblue1.text.style.fontSize = '20px';
        progressCirclesyellow1.animate(0.50); // Number from 0.0 to 1.0
    });
</script>