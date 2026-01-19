import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import NotFound from '../NotFound.vue'

describe('NotFound', () => {
  it('renders the not found message', () => {
    const wrapper = mount(NotFound)

    expect(wrapper.text()).toContain('Page Not Found')
  })
})
