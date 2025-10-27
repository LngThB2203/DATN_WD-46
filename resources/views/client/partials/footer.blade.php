<footer id="footer" class="footer">
    <div class="footer-top">
        <div class="container-fluid container-xl py-4">
            <div class="row">
                <div class="col-lg-4 mb-3">
                    <h4 class="mb-3">eStore</h4>
                    <p class="mb-1">Cửa hàng nước hoa trực tuyến.</p>
                    <p class="mb-0">Giao hàng nhanh, chính hãng.</p>
                </div>
                <div class="col-lg-4 mb-3">
                    <h5 class="mb-3">Liên hệ</h5>
                    <p class="mb-1"><i class="bi bi-telephone me-2"></i>+1 (234) 567-890</p>
                    <p class="mb-0"><i class="bi bi-envelope me-2"></i>support@example.com</p>
                </div>
                <div class="col-lg-4 mb-3">
                    <h5 class="mb-3">Theo dõi</h5>
                    <div class="d-flex gap-2">
                        <a href="#" class="text-decoration-none"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-decoration-none"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-decoration-none"><i class="bi bi-tiktok"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom py-3 border-top">
        <div class="container-fluid container-xl d-flex justify-content-between">
            <div>© <span id="year"></span> eStore. All rights reserved.</div>
            <div>
                <a href="#" class="me-3">Privacy</a>
                <a href="#">Terms</a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear()
    </script>
</footer>
