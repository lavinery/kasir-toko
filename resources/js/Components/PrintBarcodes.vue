<template>
  <div>
    <div v-for="product in products" :key="product.barcode" style="display: flex; flex-direction: column; align-items: center; margin-bottom: 10px;">
      <div>{{ product.title }}</div>
      <svg ref="barcode" />
    </div>
  </div>
</template>

<script>
import JsBarcode from 'jsbarcode';

export default {
  name: "PrintBarcodes",
  props: {
    products: {
      type: Array,
      required: true
    }
  },
  mounted() {
    this.products.forEach((product, index) => {
      JsBarcode(this.$refs.barcode[index], product.barcode, {
        format: 'CODE39',
        lineColor: '#000',
        width: 1,
        height: 20,
        displayValue: true
      });
    });
  }
};
</script>
