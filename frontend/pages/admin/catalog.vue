<template>
  <div>
    <h1 class="title-page">קטלוג</h1>

    <div class="add-product-container">
      <q-input v-model="nameForNew" label="שם" />
      <q-input v-model="priceForNew" label="מחיר ללקוח" />
      <q-input v-model="unitTypeForNew" label="סוג יחידה" />
      <q-input v-model="unitTypeCustomerForNew" label="סוג יחידה ללקוח" />
      <q-input v-model="ratioForNew" label="יחס יחידה" />

      <q-btn color="primary" label="הוספת מוצר" @click="addProduct" />
    </div>
    <q-table
      title="מוצרים"
      :rows="products"
      :columns="columns"
      row-key="name"
      @row-click="onRowClick"
    />
    <q-dialog v-model="editProduct">
      <q-card>
        <q-card-section>
          <div class="text-h6" v-text="'עריכת מוצר'" />
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-form class="edit-product-container">
            <q-input v-model="editProductName" label="שם" />
            <q-input v-model="editProductPrice" label="מחיר ללקוח" />
            <q-input v-model="editProductUnitType" label="סוג יחידה" />
            <q-input
              v-model="editProductUnitTypeCustomer"
              label="סוג יחידה ללקוח"
            />
            <q-input v-model="editProductRatio" label="יחס יחידה" />
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="ביטול" color="primary" v-close-popup />
          <q-btn flat label="אישור" color="primary" @click="saveProduct" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup lang="ts">
import Swal from "sweetalert2";

const http = useHttpStore();

const columns = [
  {
    name: "id",
    field: "id",
    label: "ID",
  },
  {
    name: "name",
    field: "name",
    label: "שם",
  },
  {
    name: "price_for_customer",
    field: "price_for_customer",
    label: "מחיר ליחידה",
  },
  {
    name: "unit_type",
    field: "unit_type",
    label: "סוג יחידה",
  },
  {
    name: "unit_type_customer",
    field: "unit_type_customer",
    label: "סוג יחידה ללקוח",
  },
  {
    name: "ratio",
    field: "ratio",
    label: "יחס",
  },
];

const products = ref([]);
async function init() {
  const res = await http.get("products");
  products.value = res;
}

onMounted(() => {
  init();
});

const nameForNew = ref("");
const priceForNew = ref(0);
const unitTypeForNew = ref("מגשים");
const unitTypeCustomerForNew = ref("מנות");
const ratioForNew = ref(30);
const addProduct = () => {
  http
    .post("products", {
      name: nameForNew.value,
      price_for_customer: priceForNew.value,
      unit_type: unitTypeForNew.value,
      unit_type_customer: unitTypeCustomerForNew.value,
      ratio: ratioForNew.value,
    })
    .then(async (res) => {
      if (res) {
        Swal.fire({
          title: "Product added",
          icon: "success",
        });
      } else {
        Swal.fire({
          title: "Product not added",
          icon: "error",
        });
      }
    })
    .then(async () => {
      products.value = await http.get("products");
    });
};

const editProduct = ref(false);
const editProductId = ref(0);
const editProductName = ref("");
const editProductPrice = ref(0);
const editProductUnitType = ref("מגשים");
const editProductUnitTypeCustomer = ref("מנות");
const editProductRatio = ref(10);

function onRowClick(evt: any, row: any) {
  if (row && row.id) {
    editProductId.value = row.id;
    editProductName.value = row.name;
    editProductPrice.value = row.price_for_customer;
    editProductUnitType.value = row.unit_type;
    editProductUnitTypeCustomer.value = row.unit_type_customer;
    editProductRatio.value = row.ratio;
    editProduct.value = true;
  }
}

async function saveProduct() {
  const res = await http.put(`products/${editProductId.value}`, {
    name: editProductName.value,
    price_for_customer: editProductPrice.value,
    unit_type: editProductUnitType.value,
    unit_type_customer: editProductUnitTypeCustomer.value,
    ratio: editProductRatio.value,
  });
  if (res) {
    editProduct.value = false;
    editProductId.value = 0;
    editProductName.value = "";
    editProductPrice.value = 0;
    editProductUnitType.value = "מגשים";
    editProductUnitTypeCustomer.value = "מנות";
    editProductRatio.value = 10;
    Swal.fire({
      title: "המוצר עודכן בהצלחה",
      icon: "success",
    });
    await init();
  } else {
    Swal.fire({
      title: "שגיאה בעדכון המוצר",
      icon: "error",
    });
  }
}
</script>

<style scoped>
.add-product-container {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}
.edit-product-container {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
</style>
