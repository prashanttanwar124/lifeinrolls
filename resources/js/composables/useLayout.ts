import { computed, reactive } from 'vue';

interface LayoutConfig {
    preset: string;
    primary: string;
    surface: string | null;
    darkTheme: boolean;
    menuMode: 'static' | 'overlay';
}

interface LayoutState {
    staticMenuInactive: boolean;
    overlayMenuActive: boolean;
    profileSidebarVisible: boolean;
    configSidebarVisible: boolean;
    sidebarExpanded: boolean;
    menuHoverActive: boolean;
    activeMenuItem: string | null;
}

const layoutConfig = reactive<LayoutConfig>({
    preset: 'Aura',
    primary: 'coral',
    surface: null,
    darkTheme: false,
    menuMode: 'static',
});

const layoutState = reactive<LayoutState>({
    staticMenuInactive: false,
    overlayMenuActive: false,
    profileSidebarVisible: false,
    configSidebarVisible: false,
    sidebarExpanded: false,
    menuHoverActive: false,
    activeMenuItem: null,
});

export function useLayout() {
    const isDarkTheme = computed(() => layoutConfig.darkTheme);

    const toggleDarkMode = () => {
        layoutConfig.darkTheme = !layoutConfig.darkTheme;

        if (layoutConfig.darkTheme) {
            document.documentElement.classList.add('dark', 'p-dark');
            localStorage.setItem('appearance', 'dark');
        } else {
            document.documentElement.classList.remove('dark', 'p-dark');
            localStorage.setItem('appearance', 'light');
        }
    };

    const toggleMenu = () => {
        if (layoutConfig.menuMode === 'overlay') {
            layoutState.overlayMenuActive = !layoutState.overlayMenuActive;
        }

        if (window.innerWidth > 991) {
            layoutState.staticMenuInactive = !layoutState.staticMenuInactive;
        } else {
            layoutState.overlayMenuActive = !layoutState.overlayMenuActive;
        }
    };

    const isSidebarActive = computed(
        () => layoutState.overlayMenuActive || !layoutState.staticMenuInactive,
    );

    return {
        layoutConfig,
        layoutState,
        isDarkTheme,
        toggleDarkMode,
        toggleMenu,
        isSidebarActive,
    };
}
