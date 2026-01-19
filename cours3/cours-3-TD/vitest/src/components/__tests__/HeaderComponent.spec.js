import { describe, it, expect } from 'vitest'
import { mount, RouterLinkStub } from '@vue/test-utils'
import HeaderComponent from '../HeaderComponent.vue'

describe('HeaderComponent', () => {
  it('renders header content and navigation links', () => {
    const wrapper = mount(HeaderComponent, {
      global: {
        stubs: {
          RouterLink: RouterLinkStub
        }
      }
    })

    expect(wrapper.text()).toContain('You did it!')

    const links = wrapper.findAllComponents(RouterLinkStub)
    const home = wrapper.find('#home')
    const demo = wrapper.find('#demo')

    expect(home.exists()).toBe(true)
    expect(demo.exists()).toBe(true)
    expect(links).toHaveLength(2)
    expect(links[0].props('to')).toBe('/')
    expect(links[1].props('to')).toBe('/demo')
  })
})
