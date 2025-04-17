<template>
  <div>
    <h1 class="title-page">הזמנות</h1>
    <q-btn color="primary" label="הוספת הזמנה" to="/admin/orders/new" />

    <q-table
      title="הזמנות"
      :rows="orders"
      :columns="columns"
      row-key="name"
      @row-click="onRowClick"
    />
  </div>
</template>

<script setup lang="ts">
const http = useHttpStore();
const router = useRouter();
const columns = [
  {
    name: "id",
    field: "id",
    label: "Order ID",
  },
  {
    name: "date",
    field: "date",
    label: "תאריך ביצוע",
  },
  {
    name: "name",
    field: "name",
    label: "שם",
  },
  {
    name: "customerName",
    field: "customer_name",
    label: "שם לקוח",
  },

  {
    name: "customerPhone",
    field: "customer_phone",
    label: "טלפון לקוח",
  },
  {
    name: "price",
    field: "price",
    label: "מחיר",
  },
];

const orders = ref([]);

onMounted(async () => {
  const res = await http.get("orders");
  orders.value = res;
});

function onRowClick(e: any, e2: any) {
  router.push(`/admin/orders/${e2.id}`);
}
</script>
<style scoped>
.catalog-container {
  padding: 20px;
}
.add-product-container {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}
</style>
