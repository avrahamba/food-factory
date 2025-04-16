<template>
  <div>Page: index</div>
  {{ test }}
</template>

<script setup lang="ts">
const http = useHttpStore();

const test: Ref<any> = ref(null);

onMounted(async () => {
  const res = await http.get("test");
  test.value = res;
});
</script>

<style scoped></style>
