<template>
  <div>
    <h1 class="title-page">קטלוג</h1>

    <q-btn color="primary" label="הוספת מוצר" @click="startAddProduct" />
    <q-table
      :grid="$q.screen.lt.sm"
      title="מוצרים"
      :rows="products"
      :columns="columns"
      row-key="name"
      @row-click="onRowClick"
    >
      <template v-slot:item="props">
        <div
          class="q-pa-xs col-xs-12 col-sm-6 col-md-4 col-lg-3 grid-style-transition"
          :style="props.selected ? 'transform: scale(0.95);' : ''"
        >
          <q-card bordered flat class="bg-grey-1">
            <q-separator />
            <q-list dense>
              <q-item v-for="col in props.cols" :key="col.name">
                <q-item-section>
                  <q-item-label v-text="col.label" />
                </q-item-section>
                <q-item-section side>
                  <q-item-label caption v-text="props.row[col.name]" />
                </q-item-section>
              </q-item>
              <q-item-section side>
                <q-item-label caption>
                  <q-btn
                    color="primary"
                    label="עריכה"
                    @click="onRowClick(null, props.row)"
                  />
                </q-item-label>
              </q-item-section>
            </q-list>
          </q-card>
        </div>
      </template>
    </q-table>
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

const editProduct = ref(false);
const editProductId = ref(0);
const editProductName = ref("");
const editProductPrice = ref(0);
const editProductUnitType = ref("מגשים");
const editProductUnitTypeCustomer = ref("מנות");
const editProductRatio = ref(10);
const startAddProduct = () => {
  editProductId.value = 0;
  editProductName.value = "";
  editProductPrice.value = 0;
  editProductUnitType.value = "מגשים";
  editProductUnitTypeCustomer.value = "מנות";
  editProductRatio.value = 10;
  editProduct.value = true;
};

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
  if (editProductId.value) {
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
  } else {
    const res = await http.post("products", {
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
      await init();
      Swal.fire({
        title: "המוצר נוסף בהצלחה",
        icon: "success",
      });
    } else {
      Swal.fire({
        title: "שגיאה בהוספת המוצר",
        icon: "error",
      });
    }
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
