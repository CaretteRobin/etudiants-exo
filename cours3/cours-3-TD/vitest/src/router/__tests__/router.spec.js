import { describe, it, expect } from 'vitest'
import router from '../index'
import HomeView from '@/views/HomeView.vue'
import DemoView from '@/views/DemoView.vue'
import NotFoundView from '@/views/NotFoundView.vue'

describe('router', () => {
  it('navigates to the home page', async () => {
    await router.push('/')
    await router.isReady()

    expect(router.currentRoute.value.name).toBe('home')
    expect(router.currentRoute.value.matched[0].components.default).toBe(HomeView)
  })

  it('navigates to the demo page', async () => {
    await router.push('/demo')
    await router.isReady()

    expect(router.currentRoute.value.name).toBe('demo')
    expect(router.currentRoute.value.matched[0].components.default).toBe(DemoView)
  })

  it('redirects to not found for unknown paths', async () => {
    await router.push('/does-not-exist')
    await router.isReady()

    expect(router.currentRoute.value.matched[0].components.default).toBe(NotFoundView)
  })
})
