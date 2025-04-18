// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: "2024-11-01",
  devtools: { enabled: true },
  runtimeConfig: {
    public: {
      env: process.env.APP_ENV,
    },
  },
  components: [{ path: "~/components" }],
  modules: [
    "@nuxt/fonts",
    "@nuxt/icon",
    "@nuxt/scripts",
    [
      "@pinia/nuxt",
      {
        storesDirs: ["./stores/**"],
      },
    ],
    "@nuxt/ui",
    "nuxt-quasar-ui",
    // "@nuxtjs/tailwindcss",
  ],
  ui: {
    // Make sure Tailwind is enabled in UI module
    tailwindcss: {
      exposeConfig: true,
      viewer: true,
    },
  },
});
