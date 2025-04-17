<template>
  <h1 class="title-page">הזמנה {{ route.params.id }}</h1>
  <div class="panel" v-if="order">
    <h2 class="order-title">פרטי הזמנה</h2>
    <div class="detailes-grid">
      <p>שם: {{ order.name }}</p>
      <p>שם לקוח: {{ order.customer_name }}</p>
      <p>טלפון לקוח: {{ order.customer_phone }}</p>
      <p>תאריך ביצוע: {{ order.date }}</p>
      <p>הצעת מחיר: {{ order.price }} ש"ח</p>
      <p>מחיר מחושב: {{ calcPrice }} ש"ח</p>
    </div>
  </div>
  <q-btn color="primary" label="הוספת מוצר" @click="editItem = true" />
  <q-table
    title="הזמנות"
    :rows="orderProducts"
    :columns="columns"
    row-key="name"
    @row-click="onRowClick"
  >
  </q-table>
  <q-dialog v-model="editItem">
    <q-card>
      <q-card-section>
        <div class="text-h6" v-text="editItemTitle" />
      </q-card-section>

      <q-card-section class="q-pt-none">
        <q-form class="add-product-container">
          <q-select
            v-model="editItemProduct"
            :options="productsForEdit"
            label="מוצר"
          />
          <q-input v-model="editItemCount" label="כמות ללקוח" />
        </q-form>
      </q-card-section>

      <q-card-actions align="right">
        <q-btn flat label="ביטול" color="primary" v-close-popup />
        <q-btn flat label="אישור" color="primary" @click="saveItem" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup lang="ts">
import Swal from "sweetalert2";
const http = useHttpStore();
const route = useRoute();

const columns = [
  {
    name: "id",
    field: "id",
    label: "Order ID",
  },
  {
    name: "name",
    field: "name",
    label: "המוצר",
  },
  {
    name: "customerCount",
    field: "customerCount",
    label: "כמות ללקוח",
  },
  {
    name: "count",
    field: "count",
    label: "כמות למטבח",
  },
];

const order = ref<any>(null);
const orderItems = ref<any[]>([]);
const products = ref<any[]>([]);
const calcPrice = ref(0);

async function init() {
  const resProduct = await http.get(`products`);
  products.value = resProduct;

  const id = route.params.id;
  const res = await http.get(`orders/${id}`);
  order.value = res.order;
  orderItems.value = res.orderItems;
  calcPrice.value = res.calcPrice;
}

onMounted(async () => {
  init();
});

const orderProducts = computed(() => {
  return orderItems.value.map((item: any) => {
    const product = products.value.find((p: any) => p.id === item.product_id);
    return {
      id: item.id,
      name: product.name,
      customerCount:
        Math.round(item.count * 100) / 100 + " " + product.unit_type_customer,
      count:
        Math.round((item.count / product.ratio) * 100) / 100 +
        " " +
        product.unit_type,
    };
  });
});

const editItem = ref(false);
const editItemId = ref(null);
const editItemCount = ref(null);
const productsForEdit = computed(() => {
  return products.value.map((p: any) => ({ label: p.name, value: p.id }));
});
const editItemProduct: Ref<{ label: string; value: number } | null> = ref(null);
const editItemTitle = computed(() => {
  return editItemId.value ? "עריכת מוצר" : "הוספת מוצר";
});

function onRowClick(e: any, e2: any) {
  editItemId.value = e2.id;
  const orderItem = orderItems.value.find((i: any) => i.id === e2.id);
  editItemCount.value = orderItem.count;
  editItemProduct.value = { label: e2.name, value: orderItem.product_id };
  editItem.value = true;
}

async function saveItem() {
  if (!editItemProduct.value || !editItemCount.value) {
    Swal.fire({
      title: "שגיאה",
      text: "יש למלא את כל השדות",
      icon: "error",
    });
    return;
  }
  if (editItemId.value) {
    const res = await http.put(
      `orders/${route.params.id}/items/${editItemId.value}`,
      {
        count: editItemCount.value,
        product_id: editItemProduct.value.value,
      }
    );
    if (res) {
      editItem.value = false;
      editItemId.value = null;
      editItemCount.value = null;
      editItemProduct.value = null;
      await init();
      Swal.fire({
        title: "המוצר עודכן בהצלחה",
        icon: "success",
      });
    } else {
      Swal.fire({
        title: "המוצר לא עודכן",
        icon: "error",
      });
    }
  } else {
    const res = await http.post(`orders/${route.params.id}/items`, {
      count: editItemCount.value,
      product_id: editItemProduct.value.value,
    });
    if (res) {
      editItem.value = false;
      editItemId.value = null;
      editItemCount.value = null;
      editItemProduct.value = null;
      await init();
      Swal.fire({
        title: "המוצר עודכן בהצלחה",
        icon: "success",
      });
    } else {
      Swal.fire({
        title: "המוצר לא עודכן",
        icon: "error",
      });
    }
  }
}
</script>

<style scoped>
.detailes-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
}
.order-title {
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 10px;
  color: #1166b6;
}
</style>
