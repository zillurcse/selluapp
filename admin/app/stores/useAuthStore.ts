export const useAuthStore = defineStore("auth", () => {
  const config = useRuntimeConfig();
  const tokenStore = useTokenStore();
  const user_cookie = useCookie<any>("auth_user", {
    maxAge: 60 * 60 * 24 * 2,
    path: "/",
  });

  const user = ref<any>(user_cookie.value || null);
  const isAuthenticated = ref(!!tokenStore.token);

  const userPermissions = computed(() => {
    const perms = new Set<string>();
    const userData = user.value;
    if (!userData) return [];

    // 1. Direct permissions
    if (Array.isArray(userData.permissions)) {
      userData.permissions.forEach((p: any) => {
        const name = typeof p === 'string' ? p : p?.name;
        if (name) perms.add(name);
      });
    }

    // 2. Role-based permissions
    if (Array.isArray(userData.roles)) {
      userData.roles.forEach((role: any) => {
        if (Array.isArray(role.permissions)) {
          role.permissions.forEach((p: any) => {
            const name = typeof p === 'string' ? p : p?.name;
            if (name) perms.add(name);
          });
        }
      });
    }

    return Array.from(perms);
  });

  const hasPermission = computed(() => {
    return (permission: string): boolean => {
      const userData = user.value;
      if (!userData) return false;

      // Super Admin always has access to everything
      if (userData.roles?.some((r: any) => r.name === 'super-admin')) return true;

      const perms = userPermissions.value;
      return perms.includes(permission) || perms.includes('*');
    };
  });

  const hasRole = computed(() => (role: string): boolean => {
    return user.value?.roles?.some((r: any) => r.name === role) || false;
  });

  async function fetchUser() {
    if (!tokenStore.getToken) return;

    try {
      const data = await $fetch<any>(`${config.public.apiBase}/user`, {
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${tokenStore.getToken}`,
        }
      });
      if (data) {
        const userData = data.data || data;
        user.value = userData;
        // Strip large data (permissions, profiles) from cookie to avoid exceeding 4KB browser limit
        user_cookie.value = {
          id: userData.id,
          name: userData.name,
          email: userData.email,
          roles: userData.roles?.map((r: any) => ({ name: r.name }))
        };
        isAuthenticated.value = true;
        return userData;
      }
    } catch (error: any) {
      console.error("Fetch user error:", error);
      // Only logout on 401 Unauthorized
      if (error.status === 401) {
        await logout();
      }
      // For 403 or other errors, we keep the session but maybe show an error
    }
  }

  async function login(form: any) {
    const utilityStore = useUtilityStore();

    try {
      utilityStore.isLoading = true;
      const res: any = await $fetch(`${config.public.apiBase}/login`, {
        method: "POST",
        body: form,
      });

      const token = res.access_token || res.token;
      if (res && token) {
        tokenStore.setToken(token);
        isAuthenticated.value = true;
        user.value = res.user;
        user_cookie.value = {
          id: res.user.id,
          name: res.user.name,
          email: res.user.email,
          roles: res.user.roles?.map((r: any) => ({ name: r.name }))
        };
        return true;
      }
      return false;
    } catch (error: any) {
      if (form && form.setErrors && error.data?.errors) {
        const errors: any[] = [];
        Object.entries(error.data.errors).forEach(([key, value]: [string, any]) => {
          errors.push({ path: key, message: value[0] });
        });
        form.setErrors(errors);
      }
      throw error;
    } finally {
      utilityStore.isLoading = false;
    }
  }

  async function logout() {
    const token = tokenStore.getToken;

    // 1. Clear local state immediately for instant feedback
    tokenStore.removeToken();
    user.value = null;
    isAuthenticated.value = false;
    user_cookie.value = null;

    // 2. Inform server in background (non-blocking)
    if (token) {
      $fetch(`${config.public.apiBase}/logout`, {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }).catch((error) => {
        console.error("Logout API error (background):", error);
      });
    }

    // 3. Redirect immediately
    return navigateTo("/login");
  }

  return {
    user,
    isAuthenticated,
    userPermissions,
    hasPermission,
    hasRole,
    fetchUser,
    login,
    logout,
  };
});
