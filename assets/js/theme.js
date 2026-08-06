'use strict';

/**
 * Colour scheme switching for the Nails CSS framework.
 *
 * The framework itself follows the operating system by default. This module
 * lets that be overridden per-browser, by writing `data-theme` onto the
 * document element and remembering the choice in localStorage.
 *
 *     NailsTheme.set('dark');    // pin to dark
 *     NailsTheme.set('light');   // pin to light
 *     NailsTheme.set('auto');    // follow the operating system (the default)
 *     NailsTheme.get();          // 'light' | 'dark' | 'auto'
 *     NailsTheme.resolved();     // 'light' | 'dark' — what is actually showing
 *
 * Any element carrying `data-theme-switcher` is wired up automatically; its
 * descendants with `data-theme-value` become the options. See
 * assets/sass/components/theme-switcher.scss for the matching markup.
 *
 * Applying the stored preference from a deferred bundle means the page paints
 * once in the wrong scheme first. To avoid that, inline the snippet returned by
 * `NailsTheme.inlineScript` in the <head>, before any stylesheet.
 */

const STORAGE_KEY = 'nails-theme';
const MODES       = ['light', 'dark', 'auto'];

/**
 * Emitted on `document` whenever the preference changes. The detail carries
 * both the preference and what it resolved to.
 */
const EVENT_CHANGE = 'nails.theme.change';

class NailsTheme {

    /**
     * The current preference, defaulting to `auto`
     *
     * A stored choice wins. Failing that, whatever the document was rendered
     * with is honoured, so that a scheme set server-side (or by the inline
     * snippet) is not thrown away when this module initialises.
     *
     * @returns {string}
     */
    static get() {
        let sStored;

        try {
            sStored = window.localStorage.getItem(STORAGE_KEY);
        } catch (e) {
            //  Private browsing, or storage otherwise unavailable
            sStored = null;
        }

        if (MODES.indexOf(sStored) !== -1) {
            return sStored;
        }

        const sRendered = document.documentElement.getAttribute('data-theme');

        return MODES.indexOf(sRendered) !== -1 ? sRendered : 'auto';
    }

    /**
     * Stores a preference and applies it immediately
     *
     * @param {string} sMode One of `light`, `dark`, or `auto`
     *
     * @returns {string} The mode which was applied
     */
    static set(sMode) {
        if (MODES.indexOf(sMode) === -1) {
            sMode = 'auto';
        }

        try {
            if (sMode === 'auto') {
                window.localStorage.removeItem(STORAGE_KEY);
            } else {
                window.localStorage.setItem(STORAGE_KEY, sMode);
            }
        } catch (e) {
            //  Not fatal; the choice simply will not survive a reload
        }

        this.apply(sMode);

        return sMode;
    }

    /**
     * Cycles through light, dark, and auto in that order
     *
     * @returns {string} The mode which was applied
     */
    static toggle() {
        return this.set(MODES[(MODES.indexOf(this.get()) + 1) % MODES.length]);
    }

    /**
     * Writes the mode onto the document element without storing it
     *
     * @param {string} [sMode] Defaults to the stored preference
     *
     * @returns {void}
     */
    static apply(sMode) {
        sMode = sMode || this.get();

        const oRoot = document.documentElement;

        //  Suppress transitions across the change, so that the components which
        //  animate their background do not lag behind those which do not
        oRoot.setAttribute('data-theme-switching', '');

        if (sMode === 'auto') {
            oRoot.removeAttribute('data-theme');
        } else {
            oRoot.setAttribute('data-theme', sMode);
        }

        //  Forcing a reflow commits the new values while transitions are still
        //  off, so they can be restored immediately afterwards
        void oRoot.offsetHeight;
        oRoot.removeAttribute('data-theme-switching');

        this.sync();

        document.dispatchEvent(new CustomEvent(EVENT_CHANGE, {
            detail: {
                mode: sMode,
                resolved: this.resolved()
            }
        }));
    }

    /**
     * The scheme actually being displayed, with `auto` resolved against the
     * operating system
     *
     * @returns {string} Either `light` or `dark`
     */
    static resolved() {
        const sMode = this.get();

        if (sMode !== 'auto') {
            return sMode;
        }

        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
            ? 'dark'
            : 'light';
    }

    /**
     * Brings every switcher on the page into line with the current preference
     *
     * @returns {void}
     */
    static sync() {
        const sMode = this.get();

        document
            .querySelectorAll('[data-theme-value]')
            .forEach(function(oOption) {
                oOption.setAttribute('aria-pressed', oOption.dataset.themeValue === sMode ? 'true' : 'false');
            });
    }

    /**
     * Wires up switchers and applies the stored preference
     *
     * Safe to call more than once; the listener is delegated from `document`,
     * so switchers added later are picked up without re-initialising.
     *
     * @returns {void}
     */
    static init() {
        if (this._bInitialised) {
            this.apply();
            return;
        }

        this._bInitialised = true;

        document.addEventListener('click', (oEvent) => {
            const oOption = oEvent.target.closest('[data-theme-value]');

            if (!oOption) {
                return;
            }

            //  Only claim the click if the option belongs to a switcher
            if (!oOption.closest('[data-theme-switcher]')) {
                return;
            }

            oEvent.preventDefault();
            this.set(oOption.dataset.themeValue);
        });

        //  While on `auto`, follow the operating system as it changes
        if (window.matchMedia) {
            const oQuery = window.matchMedia('(prefers-color-scheme: dark)');
            const fnOnChange = () => {
                if (this.get() === 'auto') {
                    this.apply('auto');
                }
            };

            if (oQuery.addEventListener) {
                oQuery.addEventListener('change', fnOnChange);
            } else if (oQuery.addListener) {
                //  Safari < 14
                oQuery.addListener(fnOnChange);
            }
        }

        this.apply();
    }
}

/**
 * A self-contained snippet which applies the stored preference before the
 * first paint. Render it inside a <script> tag in the <head>, above the
 * stylesheet, to avoid a flash of the wrong colour scheme.
 *
 * @type {string}
 */
NailsTheme.inlineScript = '(function(){try{var t=localStorage.getItem(' +
    JSON.stringify(STORAGE_KEY) +
    ');if(t==="dark"||t==="light"){document.documentElement.setAttribute("data-theme",t)}}catch(e){}})();';

NailsTheme.STORAGE_KEY  = STORAGE_KEY;
NailsTheme.MODES        = MODES;
NailsTheme.EVENT_CHANGE = EVENT_CHANGE;

export default NailsTheme;
