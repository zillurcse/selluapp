export function useApiFetch<T>(
  path: string | (() => string),
  options: UseFetchOptions<T> = {}
) {
  const config = useRuntimeConfig();

  return useFetch(path, {
    baseURL: config.public.apiBase,
    ...options,
    watch: false,
    headers: {
      Accept: "application/json",
      ...options.headers,
    },
    onRequest({ options }) {
      const tokenStore = useTokenStore();
      const token = tokenStore.token;

      const storefrontStore = useStorefrontStore();
      const vendorId = storefrontStore.vendorProfile?.user_id;

      // Send current hostname so the backend can resolve the tenant on client-side requests
      const domain = import.meta.client
        ? window.location.hostname.replace(/^www\./, '')
        : useRequestURL().hostname.replace(/^www\./, '');

      options.headers = options.headers || {};
      if (options.headers instanceof Headers) {
        options.headers.set("X-Tenant-Domain", domain);
        if (vendorId) options.headers.set("X-Vendor-Id", vendorId.toString());
        if (token) options.headers.set("Authorization", `Bearer ${token}`);
      } else {
        (options.headers as any)["X-Tenant-Domain"] = domain;
        if (vendorId) (options.headers as any)["X-Vendor-Id"] = vendorId.toString();
        if (token) (options.headers as any).Authorization = `Bearer ${token}`;
      }
    },
  });
}


