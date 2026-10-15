</div>
</main>
<footer class="app-footer text-center py-3 mt-auto">
    <small>&copy; <?= date('Y') ?> College Event Management System &middot; PHP + MongoDB &middot; Open Source Systems Lab</small>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Confirm before any delete / cancel action
document.querySelectorAll('form.confirm-action').forEach(function (form) {
    form.addEventListener('submit', function (ev) {
        if (!confirm(form.dataset.message || 'Are you sure?')) ev.preventDefault();
    });
});
// Live search filter for tables / cards
document.querySelectorAll('[data-search]').forEach(function (input) {
    input.addEventListener('keyup', function () {
        var q = input.value.toLowerCase();
        document.querySelectorAll(input.dataset.search).forEach(function (item) {
            item.style.display = item.textContent.toLowerCase().indexOf(q) > -1 ? '' : 'none';
        });
    });
});
</script>
</body>
</html>
