<template>
  <div>
    <video id="my-player"
                  class="video-js vjs-default-skin vjs-big-play-centered"
                    preload="auto"
                    autoplay
                    style="width: 100%;height: 100%;"
                    controls
                    data-setup='{"html5" : { "nativeTextTracks" : false }}'>
                <source src="rtmp://125.76.229.15:1936/live/pag/125.76.229.15/7302/026123/0/MAIN/TCP" type="rtmp/mp4">
    </video>
  </div>
</template>

<script>
  import 'videojs-flash';

export default {
    name: 'videojs',
    data() {
      return {
        videoPlayer: undefined,
        videoSrc: ''
      }
    },
methods: {
      // 选择摄像头时的触发事件，可以根据摄像头信息获取对应的直播流地址后对videoSrc进行赋值
      selectVideo() {
        this.videoSrc = 'rtmp://125.76.229.15:1936/live/pag/125.76.229.15/7302/026123/0/MAIN/TCP';
        const videoPlayer = videojs('my-player');// 关联video标签的id
        videoPlayer.src({
          src: this.videoSrc,
          type: 'rtmp/mp4'
        });
        videoPlayer.play();
      }
    }
}
  </script>
<style scoped>
</style>