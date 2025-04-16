import axios from "axios";
let activeRequests = 0;

axios.interceptors.request.use(
  (config: any) => {
    activeRequests++;
    return config;
  },
  (error: any) => {
    return Promise.reject(error);
  }
);
axios.interceptors.response.use(
  (response: any) => {
    activeRequests--;
    return response;
  },
  (error: any) => {
    activeRequests--;
    return Promise.reject(error);
  }
);

export const useHttpStore = defineStore("http", {
  state: () => ({
    callbackError: null as null | Function,
  }),
  actions: {
    async sendRequest(
      method: "get" | "post" | "put" | "delete",
      rout: string,
      data?: object,
      controller?: AbortController,
      withoutError: boolean = false
    ) {
      let baseUrl = "http://dev.food-factory.com/api/";
      const config = useRuntimeConfig();
      const env: string = config.public.env;
      if (env === "production") {
        baseUrl = "https://food-factory-api-k73e.onrender.com/api";
      }

      const options: any = { signal: controller?.signal };
      const url = baseUrl + rout;
      const res =
        method === "get"
          ? await axios.get(url, options)
          : await axios[method](url, data, options);

      const resData = res.data;
      if (withoutError) return resData;

      if (resData.success) {
        if (resData.data?.notes?.length) {
          resData.data.notes.forEach((note: any) => {
            if (this.callbackError) this.callbackError(note);
          });
        }
        return resData.data || true;
      }

      return resData;
    },
    async get(
      rout: string,
      data: object = {},
      controller: AbortController | undefined = undefined,
      withoutError: boolean = false
    ) {
      if (JSON.stringify(data) === "{}")
        return this.sendRequest(
          "get",
          rout,
          undefined,
          controller,
          withoutError
        );
      const jsonString = JSON.stringify(data);
      const queryString = jsonString
        .replace(/[{}"]/g, "")
        .replace(/:/g, "=")
        .replace(/,/g, "&");
      return this.sendRequest(
        "get",
        `${rout}?${queryString}`,
        undefined,
        controller,
        withoutError
      );
    },
    async post(
      rout: string,
      data: any = {},
      controller: AbortController | undefined = undefined,
      withoutError: boolean = false
    ) {
      return this.sendRequest("post", rout, data, controller, withoutError);
    },
    async put(
      rout: string,
      data: any = {},
      controller: AbortController | undefined = undefined,
      withoutError: boolean = false
    ) {
      return this.sendRequest("put", rout, data, controller, withoutError);
    },
    async delete(
      rout: string,
      data: any = {},
      controller: AbortController | undefined = undefined,
      withoutError: boolean = false
    ) {
      return this.sendRequest("delete", rout, data, controller, withoutError);
    },
  },
});
