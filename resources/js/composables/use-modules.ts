export interface ModuleInfo {
  name: string
  enabled: boolean
  group: string
  label: string
}

const bootstrapModules: ModuleInfo[] = (window as any).bootstrap?.modules ?? []

export function useModules() {
  function isEnabled(name: string): boolean {
    return bootstrapModules.some(m => m.name === name && m.enabled)
  }

  function findByGroup(group: string): ModuleInfo[] {
    return bootstrapModules.filter(m => m.group === group)
  }

  function getModule(name: string): ModuleInfo | undefined {
    return bootstrapModules.find(m => m.name === name)
  }

  return {
    isEnabled,
    findByGroup,
    getModule,
    modules: bootstrapModules as readonly ModuleInfo[],
  }
}
