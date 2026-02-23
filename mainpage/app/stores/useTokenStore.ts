export const useTokenStore = defineStore("token", () => {
  const cookie = useCookie<string | null>("auth_token", {
    maxAge: 60 * 60 * 24 * 7,
    path: "/",
  });

  const token = ref<string | null>(cookie.value || null);
  const loggedIn = ref(!!cookie.value);

  const getToken = computed(() => token.value);
  const getStatus = computed(() => loggedIn.value);
  const isAuthenticated = computed(() => !!token.value);

  function setToken(newToken: string) {
    token.value = newToken;
    loggedIn.value = true;
    cookie.value = newToken;
  }

  function removeToken() {
    cookie.value = null;
    token.value = null;
    loggedIn.value = false;
  }

  return {
    token,
    loggedIn,
    getToken,
    getStatus,
    isAuthenticated,
    setToken,
    removeToken,
  };
});


