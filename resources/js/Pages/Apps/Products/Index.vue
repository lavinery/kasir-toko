<template>
    <Head>
        <title>Products - Aplikasi Kasir</title>
    </Head>
    <main class="c-main">
        <div class="container-fluid">
            <div class="fade-in">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 rounded-3 shadow border-top-purple">
                            <div class="card-header d-flex justify-content-between">
                                <span class="font-weight-bold"><i class="fa fa-shopping-bag"></i> PRODUCTS</span>
                            </div>
                            <div class="card-body">
                                <form @submit.prevent="handleSearch">
                                    <div class="input-group mb-3">
                                        <Link href="/apps/products/create" v-if="hasAnyPermission(['products.create'])" class="btn btn-primary input-group-text"> <i class="fa fa-plus-circle me-2"></i> NEW</Link>
                                        <input type="text" class="form-control" v-model="search" placeholder="search by product title...">
                                        <button class="btn btn-primary input-group-text" type="submit"> <i class="fa fa-search me-2"></i> SEARCH</button>
                                    </div>
                                </form>
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">
                                                <label>
                                                    Select Barcode
                                                </label>
                                            </th>
                                            <th scope="col">Barcode</th>
                                            <th scope="col">Title</th>
                                            <th scope="col">Buy Price</th>
                                            <th scope="col">Sell Price</th>
                                            <th scope="col">Stock</th>
                                            <th scope="col" style="width:20%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(product, index) in products.data" :key="index">
                                            <td class="text-center">
                                                <input type="checkbox" :value="product" v-model="selectedProducts" />
                                            </td>
                                            <td class="text-center">
                                                <Barcode
                                                    :value="product.barcode"
                                                    :format="'CODE39'"
                                                    :lineColor="'#000'"
                                                    :width="1"
                                                    :height="20"
                                                    :productName="product.title" 
                                                />
                                            </td>
                                            <td>{{ product.title }}</td>
                                            <td>Rp. {{ formatPrice(product.buy_price) }}</td>
                                            <td>Rp. {{ formatPrice(product.sell_price) }}</td>
                                            <td>{{ product.stock }}</td>
                                            <td class="text-center">
                                                <Link :href="`/apps/products/${product.id}/edit`" v-if="hasAnyPermission(['products.edit'])" class="btn btn-success btn-sm me-2"><i class="fa fa-pencil-alt me-1"></i> EDIT</Link>
                                                <button @click.prevent="destroy(product.id)" v-if="hasAnyPermission(['products.delete'])" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> DELETE</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-start mb-3">
                                    <button class="btn btn-primary" @click="printSelectedBarcodes">Print Selected</button>
                                </div>
                                <Pagination :links="products.links" align="end"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Print Selected Barcodes Modal -->
        <div class="modal fade" id="printModal" tabindex="-1" role="dialog" aria-labelledby="printModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="printModalLabel">Print Selected Barcodes</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row justify-content-start">
                            <div class="col-md-12">
                                <div v-for="(product, index) in selectedProducts" :key="index" class="d-flex flex-column align-items-center mb-3">
                                    <div>{{ product.title }}</div>
                                    <div>
                                        <svg :id="'barcode-' + product.barcode"></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" @click="generateBarcodes">Print</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
//import layout
import LayoutApp from '../../../Layouts/App.vue';

//import component pagination
import Pagination from '../../../Components/Pagination.vue';

//import Head and Link from Inertia
import { Head, Link, router } from '@inertiajs/vue3';

//import ref from vue
import { ref } from 'vue';

//import sweet alert2
import Swal from 'sweetalert2';

//import component barcode
import Barcode from '../../../Components/Barcode.vue';

//import jsbarcode
import JsBarcode from 'jsbarcode';

export default {
    //layout
    layout: LayoutApp,

    //register components
    components: {
        Head,
        Link,
        Pagination,
        Barcode
    },

    //props
    props: {
        products: Object,
    },

    //composition API
    setup() {
        //define state search
        const search = ref('' || (new URL(document.location)).searchParams.get('q'));

        //define state selectedProducts
        const selectedProducts = ref([]);

        //define method search
        const handleSearch = () => {
            router.get('/apps/products', {
                //send params "q" with value from state "search"
                q: search.value,
            });
        }

        //method to select all products
        //method to select all products
const selectAll = (event) => {
    if (event.target.checked) {
        // Menggunakan spread operator untuk menambahkan semua produk ke selectedProducts.value
        selectedProducts.value = [...products.data];
    } else {
        selectedProducts.value = []; // Kosongkan selectedProducts jika checkbox tidak diceklis
    }
}


        //method "destroy"
        const destroy = (id) => {
            //show confirm
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                if (result.isConfirmed) {
                    //send to server
                    router.delete(`/apps/products/${id}`);

                    //show alert
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Product deleted successfully.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            })
        }

        //method to open print modal and generate barcodes
        const printSelectedBarcodes = () => {
            // Open the modal
            const printModal = new bootstrap.Modal(document.getElementById('printModal'), {
                backdrop: 'static',
                keyboard: false
            });
            printModal.show();

            // Generate barcodes
            selectedProducts.value.forEach(product => {
                JsBarcode(`#barcode-${product.barcode}`, product.barcode, {
                    format: 'CODE39',
                    lineColor: '#000',
                    width: 1,
                    height: 20,
                    displayValue: true
                });
            });
        }

        //method to print barcodes from modal
        const generateBarcodes = () => {
            const printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Print Barcodes</title></head><body>');

            selectedProducts.value.forEach(product => {
                printWindow.document.write('<div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 10px;">');
                printWindow.document.write(`<div>${product.title}</div>`);
                printWindow.document.write(`<svg id="barcode-${product.barcode}"></svg>`);
                printWindow.document.write('</div>');
            });

            printWindow.document.write('</body></html>');
            printWindow.document.close();

            selectedProducts.value.forEach(product => {
                JsBarcode(`#barcode-${product.barcode}`, product.barcode, {
                    format: 'CODE39',
                    lineColor: '#000',
                    width: 1,
                    height: 20,
                    displayValue: true
                });
            });

            // Print the window content
            printWindow.print();
        }

        return {
            search,
            selectedProducts,
            handleSearch,
            selectAll,
            destroy,
            printSelectedBarcodes,
            generateBarcodes
        }
    }
}
</script>

<style>

</style>
