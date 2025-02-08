// vite.config.js
import { build, defineConfig } from "file:///C:/Users/App4Media%20GmbH/Herd/bfr/node_modules/vite/dist/node/index.js";
import laravel from "file:///C:/Users/App4Media%20GmbH/Herd/bfr/node_modules/laravel-vite-plugin/dist/index.js";
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: [
        "resources/css/app.css",
        "resources/js/app.js"
      ],
      refresh: true
    }),
    build({
      minify: true,
      sourcemap: true,
      rollupOptions: {
        output: {
          manualChunks: () => {
            return "vendor";
          }
        }
      }
    })
  ]
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFxVc2Vyc1xcXFxBcHA0TWVkaWEgR21iSFxcXFxIZXJkXFxcXGJmclwiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9maWxlbmFtZSA9IFwiQzpcXFxcVXNlcnNcXFxcQXBwNE1lZGlhIEdtYkhcXFxcSGVyZFxcXFxiZnJcXFxcdml0ZS5jb25maWcuanNcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfaW1wb3J0X21ldGFfdXJsID0gXCJmaWxlOi8vL0M6L1VzZXJzL0FwcDRNZWRpYSUyMEdtYkgvSGVyZC9iZnIvdml0ZS5jb25maWcuanNcIjtpbXBvcnQge2J1aWxkLCBkZWZpbmVDb25maWd9IGZyb20gJ3ZpdGUnO1xuaW1wb3J0IGxhcmF2ZWwgZnJvbSAnbGFyYXZlbC12aXRlLXBsdWdpbic7XG5cbmV4cG9ydCBkZWZhdWx0IGRlZmluZUNvbmZpZyh7XG4gICAgcGx1Z2luczogW1xuICAgICAgICBsYXJhdmVsKHtcbiAgICAgICAgICAgIGlucHV0OiBbXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9jc3MvYXBwLmNzcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9hcHAuanMnLFxuICAgICAgICAgICAgXSxcbiAgICAgICAgICAgIHJlZnJlc2g6IHRydWUsXG4gICAgICAgIH0pLFxuXG4gICAgICAgIGJ1aWxkKHtcbiAgICAgICAgICAgIG1pbmlmeTogdHJ1ZSxcbiAgICAgICAgICAgIHNvdXJjZW1hcDogdHJ1ZSxcblxuICAgICAgICAgICAgcm9sbHVwT3B0aW9uczoge1xuICAgICAgICAgICAgICAgIG91dHB1dDoge1xuICAgICAgICAgICAgICAgICAgICBtYW51YWxDaHVua3M6ICgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiAndmVuZG9yJztcblxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSlcbiAgICBdLFxufSk7XG4iXSwKICAibWFwcGluZ3MiOiAiO0FBQThSLFNBQVEsT0FBTyxvQkFBbUI7QUFDaFUsT0FBTyxhQUFhO0FBRXBCLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBQ3hCLFNBQVM7QUFBQSxJQUNMLFFBQVE7QUFBQSxNQUNKLE9BQU87QUFBQSxRQUNIO0FBQUEsUUFDQTtBQUFBLE1BQ0o7QUFBQSxNQUNBLFNBQVM7QUFBQSxJQUNiLENBQUM7QUFBQSxJQUVELE1BQU07QUFBQSxNQUNGLFFBQVE7QUFBQSxNQUNSLFdBQVc7QUFBQSxNQUVYLGVBQWU7QUFBQSxRQUNYLFFBQVE7QUFBQSxVQUNKLGNBQWMsTUFBTTtBQUNoQixtQkFBTztBQUFBLFVBRVg7QUFBQSxRQUNKO0FBQUEsTUFDSjtBQUFBLElBQ0osQ0FBQztBQUFBLEVBQ0w7QUFDSixDQUFDOyIsCiAgIm5hbWVzIjogW10KfQo=
