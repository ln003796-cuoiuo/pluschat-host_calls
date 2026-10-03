export class PlusCall {
  constructor({token, callId, wsUrl, iceServers=[], onEvent=()=>{}, onRemote=()=>{}}){this.token=token;this.callId=callId;this.wsUrl=wsUrl;this.iceServers=iceServers;this.onEvent=onEvent;this.onRemote=onRemote;this.ws=null;this.pc=null;this.stream=null;this.screen=null;}
  async start({video=false,screen=false}={}){this.stream=await navigator.mediaDevices.getUserMedia({audio:true,video});if(screen)this.screen=await navigator.mediaDevices.getDisplayMedia({video:true});const s=this.screen||this.stream;for(const t of s.getTracks())this.pc?.addTrack(t,s);this.connect();}
  connect(){this.ws=new WebSocket(this.wsUrl);this.ws.onopen=()=>this.ws.send(JSON.stringify({type:'join',call_id:this.callId,token:this.token}));this.ws.onmessage=async e=>{const m=JSON.parse(e.data);this.onEvent(m);};}
  toggleMic(){if(this.stream)this.stream.getAudioTracks().forEach(t=>t.enabled=!t.enabled);}
  toggleCamera(){if(this.stream)this.stream.getVideoTracks().forEach(t=>t.enabled=!t.enabled);}
  async hangup(){this.ws?.send(JSON.stringify({type:'leave',call_id:this.callId,token:this.token}));this.ws?.close();this.pc?.close();this.stream?.getTracks().forEach(t=>t.stop());this.screen?.getTracks().forEach(t=>t.stop());}
}
