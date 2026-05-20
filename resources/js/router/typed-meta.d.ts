import 'vue-router'

declare module 'vue-router' {
  interface RouteMeta {
    requiredRole?: string
    requiredPermission?: string
  }
}
