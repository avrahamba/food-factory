<template>
  <h1 class="title-page">הוספת הזמנה</h1>

  <div class="panel">
    <q-form class="add-product-container">
      <q-input class="right-label-input" v-model="name" label="שם" />
      <q-input v-model="customerName" label="שם לקוח" />
      <q-input v-model="customerPhone" label="טלפון לקוח" />
      <q-input type="date" v-model="date" label="תאריך ביצוע" />
      <q-input type="number" v-model="price" label="הצעת מחיר" />

      <q-btn color="primary" label="הוספת הזמנה" @click="createOrder" />
    </q-form>
  </div>
</template>

<script setup lang="ts">
const http = useHttpStore();
const router = useRouter();
import Swal from "sweetalert2";

const name = ref("");
const customerName = ref("");
const customerPhone = ref("");
const price = ref(0);
const date = ref("");

const createOrder = async () => {
  const res = await http.post("orders", {
    name: name.value,
    customer_name: customerName.value,
    customer_phone: customerPhone.value,
    price: price.value,
  });
  if (res && res.id) {
    router.push(`/orders/${res.id}`);
  } else {
    Swal.fire({
      title: "הזמנה לא נוצרה",
      icon: "error",
    });
  }
};
</script>

<style scoped>
.add-product-container {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
  margin-bottom: 20px;
}
</style>
