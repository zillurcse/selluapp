import { formError } from '~/utils';
import { ref } from 'vue'
import { toast } from 'vue-sonner';

export default function useCrud() {
  const route = useRoute();
  const store = useUtilityStore();
  const tokenStore = useTokenStore();
  const config = useRuntimeConfig();
  const baseURL = config.public.apiBase;

  // Set isEdit based on route params
  if (route.params.id === undefined) store.isEdit = false;
  else store.isEdit = true;

  const getHeaders = () => {
    const headers: any = {
      Accept: "application/json",
    };
    if (tokenStore.token) {
      headers.Authorization = `Bearer ${tokenStore.token}`;
    }
    return headers;
  };

  /**
   * Create or Update an item
   */
  const createItem = async (apiEndpoint: string, formData: any, form: any = null, autoRedirect: boolean = true) => {
    // Handle form clearing/resetting
    if (form) {
      if (typeof form === 'string') {
        const formEl = document.getElementById(form) as HTMLFormElement;
        if (formEl) formEl.reset();
      } else if (form.value && typeof form.value.clear === 'function') {
        form.value.clear();
      }
    }

    // Construct API URL
    const api = store.isEdit && route.params.id !== undefined
      ? `${apiEndpoint}/${route.params.id}`
      : apiEndpoint;

    store.isLoading = true;

    // For Laravel to handle FormData with PUT, we use POST with _method=PUT
    let method = store.isEdit ? "PUT" : "POST";
    let bodyData = formData;

    if (store.isEdit && formData instanceof FormData) {
      method = "POST";
      if (!formData.has('_method')) {
        formData.append('_method', 'PUT');
      }
    }

    try {
      const response = await $fetch<any>(api, {
        baseURL,
        method: method as any,
        body: bodyData,
        headers: getHeaders(),
      });

      store.isLoading = false;
      const successMsg = response?.message || response?.msg || "Item created successfully";
      toast.success(successMsg);

      store.formData = {};

      // Redirect logic
      if (autoRedirect) {
        if (store.isCreateMore) {
          navigateTo(`${store.pageBackLink}/create`);
        } else {
          navigateTo(store.pageBackLink);
        }
      }
      return response;
    } catch (err: any) {// Changed error to err for consistency with provided snippet
      store.isLoading = false;
      if (err.status === 422 && form) { // Changed error to err
        if (typeof form === 'object' && form.value) {
          formError(err.data?.errors, form); // Changed error to err
        }
      }
      // Extract error message from response if available
      const message = err.data?.message || err.message || 'Failed to create item'; // Changed error to err
      toast.error(message);
      throw err; // Changed error to err
    }
  };

  /**
   * Get a single item
   */
  const getItem = async (apiEndpoint: string) => {
    return await $fetch(apiEndpoint, {
      baseURL,
      headers: getHeaders()
    });
  };

  /**
   * Get a single item by ID
   */
  const getById = async (apiEndpoint: string, id: string | number) => {
    return await $fetch(`${apiEndpoint}/${id}`, {
      baseURL,
      headers: getHeaders()
    });
  };

  /**
   * Get all items (list)
   */
  const getAll = async (apiEndpoint: string, query: any = {}) => {
    return await $fetch(apiEndpoint, {
      baseURL,
      query,
      headers: getHeaders()
    });
  };

  /**
   * Navigate to edit page
   */
  const editItem = (item_id: number | string, apiEndpoint: string) => {
    store.isEdit = true;
    navigateTo(`${apiEndpoint}/${item_id}`);
  };

  /**
   * Delete an item
   */
  const deleteItem = async (item_id: number | string, apiEndpoint: string) => {
    store.isLoading = true;
    try {
      const response = await $fetch<any>(`${apiEndpoint}/${item_id}`, {
        baseURL,
        method: "DELETE",
        headers: getHeaders(),
      });

      const msg = response?.message || response?.msg || response?.success || "Item deleted successfully";
      toast.success(msg);
      return response;
    } catch (error: any) {
      console.error("Error deleting item:", error);
      toast.error(error.data?.message || error.message || "Failed to delete item");
      throw error;
    } finally {
      store.isLoading = false;
    }
  };

  /**
   * Update Settings (Special case)
   */
  const updateSettings = async (settingsData: any[]) => {
    store.isLoading = true;
    try {
      const response = await $fetch<any>('/settings', {
        baseURL,
        method: 'POST',
        body: { settings: settingsData },
        headers: getHeaders()
      });
      store.isLoading = false;
      const settingsMsg = response?.message || response?.msg || response?.success || "Settings updated";
      toast.success(settingsMsg);
      return response;
    } catch (error: any) {
      store.isLoading = false;
      toast.error(error.data?.message || error.message || "Failed to update settings");
      throw error;
    }
  };

  /*
   * Update an item
   */
  const updateItem = async (apiEndpoint: string, formData: any, form: any = null) => {
    store.isLoading = true;

    // For Laravel to handle FormData with PUT, we use POST with _method=PUT
    let method = "PUT";
    let bodyData = formData;

    if (formData instanceof FormData) {
      method = "POST";
      if (!formData.has('_method')) {
        formData.append('_method', 'PUT');
      }
    }

    try {
      const response = await $fetch<any>(apiEndpoint, {
        baseURL,
        method: method as any,
        body: bodyData,
        headers: getHeaders(),
      });

      store.isLoading = false;
      const successMsg = response?.message;
      if (successMsg) {
        toast.success(successMsg);
      } else {
        toast.success('Item updated successfully');
      }

      // Redirect logic could be here or handled in component
      return response;
    } catch (err: any) {
      store.isLoading = false;
      if (err.status === 422 && form) {
        if (typeof form === 'object' && form.value) {
          formError(err.data?.errors, form);
        }
      }
      const message = err.data?.message || err.message || 'Failed to update item';
      toast.error(message);
      throw err;
    }
  };
  const duplicateItem = async () => { };

  //barcode print
  const barcodePrint = async (apiEndpoint: string, data: any = {}) => {
    store.isLoading = true;
    try {
      const response = await $fetch<any>(apiEndpoint, {
        baseURL,
        method: 'POST',
        headers: getHeaders(),
        body: data
      });
      store.isLoading = false;
      return response;
    } catch (err: any) {
      store.isLoading = false;
      const message = err.data?.message || err.message || 'Failed to print labels';
      toast.error(message);
      throw err;
    }
  };

  return {
    createItem,
    getItem,
    getById,
    getAll,
    editItem,
    deleteItem,
    updateItem,
    duplicateItem,
    barcodePrint,
    updateSettings,
    getHeaders
  };
}

