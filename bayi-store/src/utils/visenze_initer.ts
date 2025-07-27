//@ts-nocheck
import { useShopStore } from "../stores/shopStore";

export const VisenzeApi = (() => {
  const shopStore = useShopStore()
  const aiSupport = () => shopStore.getStore?.ai_support
  const __recommendation = {
    init: async () => {
      if (!aiSupport()) return
      console.info("VZ initializing...");
      !(function (x, e, t, n, r, i, a) {
        var o =
          localStorage.getItem("va-uid") ||
          (function x() {
            let e = new Date().getTime(),
              t = "xxxxxxxx.xxxx.4xxx.yxxx.xxxxxxxxxxxx".replace(
                /[xy]/g,
                (x) => {
                  let t = (e + 16 * Math.random()) % 16 | 0;
                  return (
                    (e = Math.floor(e / 16)),
                    ("x" === x ? t : (3 & t) | 8).toString(16)
                  );
                }
              );
            return t;
          })(),
          c = x.getElementsByTagName(e)[0],
          d = x.createElement(e),
          g = new URL(
            `https://search.visenze.com/v2/widget-init?app_key=${t}&placement_id=${n}&container=${r}&uid=${o}`
          );
        i && (g += `&contexts=${i}`),
          (d.async = !0),
          (d.src = g),
          (d.onload = function () {
            a && a();
          }),
          c.parentNode.insertBefore(d, c);
      })(
        document,
        "script",
        "5ff7cf7b72bb5e46d604096ec712d2da",
        "5384",
        ".ps-widget-5384"
      );
      console.info("VZ initialized...");
    },
    dispose: async () => {
      if (!aiSupport()) return
      if (window.visenzeWidget5384) {
        console.info("VZ Disposed");
        visenzeWidget5384.disposeWidget();
      }
    },
  };
  const __search = {
    init: async () => {
      if (!aiSupport()) return
      !(function (x, e, t, n, r, i, a) {
        var o =
          localStorage.getItem("va-uid") ||
          (function x() {
            let e = new Date().getTime(),
              t = "xxxxxxxx.xxxx.4xxx.yxxx.xxxxxxxxxxxx".replace(
                /[xy]/g,
                (x) => {
                  let t = (e + 16 * Math.random()) % 16 | 0;
                  return (
                    (e = Math.floor(e / 16)),
                    ("x" === x ? t : (3 & t) | 8).toString(16)
                  );
                }
              );
            return t;
          })(),
          c = x.getElementsByTagName(e)[0],
          d = x.createElement(e),
          g = new URL(
            `https://search.visenze.com/v2/widget-init?app_key=${t}&placement_id=${n}&container=${r}&uid=${o}`
          );
        i && (g += `&contexts=${i}`),
          (d.async = !0),
          (d.src = g),
          (d.onload = function () {
            a && a();
          }),
          c.parentNode.insertBefore(d, c);
      })(
        document,
        "script",
        "af4630316430cf9559745089241d25b8",
        "5382",
        ".ps-widget-5382"
      );
    },
    dispose: async () => {
      if (!aiSupport()) return
      if (window.visenzeWidget5382) {
        console.info("VZ Disposed");
        visenzeWidget5382.disposeWidget();
      }
    },
  };

  return {
    searchWidget: __search,
    recommWidget: __recommendation,
  };
})();
