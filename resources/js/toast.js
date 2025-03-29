/**
 * Simple toast notification system
 */
import { createToaster } from "@meforma/vue-toaster";

const toast = createToaster({
  position: "top-right",
  duration: 3000,
});

export default {
  success(message) {
    toast.success(message);
  },
  error(message) {
    toast.error(message);
  },
  info(message) {
    toast.info(message);
  },
  warning(message) {
    toast.warning(message);
  }
}; 