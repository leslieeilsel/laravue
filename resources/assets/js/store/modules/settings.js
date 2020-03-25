import defaultSettings from '../../settings';

const {title, appUrl} = defaultSettings;
const state = {
  title: title,
  appUrl: appUrl,
};

export default {
  namespaced: true,
  state,
};

