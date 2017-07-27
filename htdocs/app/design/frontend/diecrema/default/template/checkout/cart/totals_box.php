<div class="totals">
    <?php echo $this->getChildHtml('totals'); ?>
    <?php if(!$this->hasError()): ?>
    <ul class="checkout-types">
    <?php foreach ($this->getMethods('methods') as $method): ?>
        <?php if ($methodHtml = $this->getMethodHtml($method)): ?>
        <li><?php echo $methodHtml; ?></li>
        <?php endif; ?>
    <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</div>