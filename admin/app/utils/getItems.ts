import type { UseFetchOptions } from 'nuxt/app';

const items = async <T = any>(
  apiEndpoint: string,
  query: Record<string, any> = {},
  options: UseFetchOptions<T> = {}
) => {
  const response = await useApiFetch<T>(apiEndpoint, {
    query,
    ...options,
  });

  return response;
};

export default { items };
