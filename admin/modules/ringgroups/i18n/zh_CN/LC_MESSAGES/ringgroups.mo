��    V      �     |      x     y     �  =   �     �  
   �     �     �  '     E   /     u     �     �     �     �     �    �     �	     
     
     .
  �   4
     �
     �
     �
  �     �   �  �   >  �   �     �  j   �       &   +     R  0   r     �     �  
   �     �     �     �     �       7        J     �     �     �  +   �  @   '  �   h     e  �   t              '  �   4          (     F     `     q     y     �     �     �     �     �     �     �     �       
   1     <     T     r     �  	   �     �     �     �     �     �  %   �  <        S  �  [     
  #     9   A     {     �  	   �     �  -   �  ?   �     (     5     E     L     Y     u    �     �     �     �     �  x   �  !   H     j     �  �   �  �   -  �   �  �   X       v   	     �     �     �  -   �     �               #     6     H     _     l  E   |  �   �     J      ]      s   !   �   <   �     �      �!  u   "     w"  '   �"  #  �"  �   �#  $   �$  )   �$  %   %     8%     K%  	   R%  !   \%  &   ~%  "   �%     �%     �%     �%     �%     &     &     &     -&  #   L&     p&     �&     �&     �&     �&     �&     �&     �&  $   '  K   *'     v'             6          V                    
                              @   0   (          R       1      >   *   5   7   #                 9   J   F   '      +   ;   )                   M   K   H   !   2   :                 I   8           E       N   .   &   L   Q      A   C   3   ?   4      "          P   <                        T                  G   -              	          O         D   ,          $                   B       /       U   =             S      %       (pick extension) *-prim ALERT_INFO can be used for distinctive ring with SIP devices. Add Ring Group Alert Info Announcement: CID Name Prefix Checking if recordings need migration.. Choose an extension to append to the end of the extension list above. Confirm Calls Conflicting Extensions Default Delete Group Destination if no answer Edit Ring Group Enable this if you're calling external numbers that need confirmation - eg, a mobile phone may go to voicemail which will pick up the call. Enabling this requires the remote side push 1 on their phone before the call is put through. This feature only works with the ringall ring strategy Extension List Extension Quick Pick Group Description INUSE If you select a Music on Hold class to play, instead of 'Ring', they will hear that instead of Ringing while they are waiting for someone to pick up. Ignore CF Settings Invalid Group Number specified Invalid time specified Message to be played to the caller before dialing this group.<br><br>To add additional recordings please use the "System Recordings" MENU to the left Message to be played to the caller before dialing this group.<br><br>You must install and enable the "Systems Recordings" Module to edit this option Message to be played to the person RECEIVING the call, if 'Confirm Calls' is enabled.<br><br>To add additional recordings use the "System Recordings" MENU to the left Message to be played to the person RECEIVING the call, if the call has already been accepted before they push 1.<br><br>To add additional recordings use the "System Recordings" MENU to the left None Only ringall, ringallv2, hunt and the respective -prim versions are supported when confirmation is checked Play Music On Hold? Please enter a valid Group Description Please enter an extension list. Provide a descriptive title for this Ring Group. Remote Announce: Ring Ring Group Ring Group %s:  Ring Group: %s Ring Group: %s (%s) Ring Groups Ring Strategy: Ring all available channels until one answers (default) Ring first extension in the list, then ring the 1st and 2nd extension, then ring 1st 2nd and 3rd extension in the list.... etc. Ring-Group Number Skip Busy Agent Submit Changes Take turns ringing each available extension The number users will dial to ring extensions in this ring group These modes act as described above. However, if the primary extension (first in list) is occupied, the other extensions will not be rung. If the primary is FreePBX DND, it won't be rung. If the primary is FreePBX CF unconditional, then all will be rung This ringgroup Time in seconds that the phones will ring. For all hunt style ring strategies, this is the time for each iteration of phone(s) that are rung Too-Late Announce: Warning! Extension When checked, agents who are on an occupied phone will be skipped as if the line were returning busy. This means that Call Waiting or multi-line phones will not be presented with the call and in the various hunt style ring strategies, the next agent will be attempted. When checked, agents who attempt to Call Forward will be ignored, this applies to CF, CFU and CFB. Extensions entered with '#' at the end, for example to access the extension's Follow-Me, might not honor this setting . adding annmsg_id field.. adding remotealert_id field.. adding toolate_id field.. already migrated default deleted dropping annmsg field.. dropping remotealert field.. dropping toolate field.. fatal error firstavailable firstnotonphone hunt is already in use is not allowed for your account memoryhunt migrate annmsg to ids.. migrate remotealert to  ids.. migrate toolate to ids.. migrated %s entries migrating no annmsg field??? no remotealert field??? no toolate field??? none ok ring only the first available channel ring only the first channel which is not offhook - ignore CW ringall Project-Id-Version: FreePBX 2.5 Chinese Translation
Report-Msgid-Bugs-To: 
POT-Creation-Date: 2015-02-20 12:35-0500
PO-Revision-Date: 2009-02-01 18:54+0800
Last-Translator: 周征晟 <zhougongjizhe@163.com>
Language-Team: EdwardBadBoy <zhougongjizhe@163.com>
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
X-Poedit-Language: Chinese
X-Poedit-Country: CHINA
X-Poedit-SourceCharset: utf-8
 （选取分机） 依从主分机（策略名-prim） 警告信息可以用于为SIP设备产生独特的铃声 添加拨号小组 警告信息 通告： 主叫ID名的前缀 正在检查录音是否需要迁移。。。 选择一个分机以添加到上面的分机列表的末尾。 呼叫确认 分机号冲突 默认 删除小组 无人接听时的目的地 编辑拨号小组 如果你要呼叫需要确认的外部号码时，就启用此项——比如，一个移动电话会被转移，而由语音邮箱接听。要启用这个选项，需要远端在接通前在电话上按下1。这个功能只会在全部响铃的策略下起作用。 分机列表 快速分机选取 小组描述 正在使用 如果你选择了一个等待音乐类别，而不是“振铃”，呼叫者在等待接听的时候会听到音乐。 忽略呼叫转移的相关设置 指定了无效的组号码 指定了无效的时间 在拨打这个小组之前，要播放给主叫的消息。<br /><br />要添加额外的录音，请使用左边的“系统录音”菜单 在拨打这个小组之前，要播放给主叫的消息。<br /><br />请安装并启用“系统录音”模块以编辑这个选项 如果“呼叫确认”被启用，这是对接听呼叫的人播放的消息。<br /><br />要添加额外的录音，请使用左边的“系统录音”菜单 如果呼叫被接听，却还没来得及按下1键，这是要对接听者播放的消息<br /><br />要添加而外的录音，请使用左边的“系统录音”菜单 无 若“确认”选项被启用，就只支持全部响铃、全部响铃2、搜寻和各自的主分机依从策略。 播放等待音乐？ 请输入有效的组描述 请输入一个分机列表。 为拨号小组提供一个描述性的标题 远程公告： 振铃 拨号小组 拨号小组 %s： 拨号小组：%s 拨号小组：%s (%s) 拨号小组 振铃策略： 全部可用频道都响铃直到其中一个接听（默认设置） 首先使列表中第一个分机响铃，然后是第一个和第二个响，接着是第一、二、三个响。。。以次类推。 拨号小组号码 跳过忙碌的坐席 提交更改 在可用的分机上轮流响铃 用户拨打此号码以呼叫这个拨号小组中的分机 这些模式按上述的方式工作。然而，如果主分机（列表中的第一个）占线，其他的分机就不会响铃。如果主分机是设置了免打扰，它就不会振铃。如果主分机设置了无条件转移呼叫，那么所有的分机会响铃 这个拨号小组 电话响铃的秒数。对于所有的搜寻式的响铃策略，这是每次搜寻出的电话的响铃的时间。 按键太晚公告： 警告！你的帐户无法使用分机 如果选择了此项，在一个占线的电话上的坐席将会被跳过，它的线路将被视为忙碌。这导致有呼叫等待功能的电话，或具有多根线路的电话，在占线时都会被跳过，然后呼叫根据自己的搜寻策略去尝试下一个可用的坐席。 如果选择了此项，使用了呼叫转移功能（CF）的坐席将被忽略，这适用于CF、CFU和CFB。以“#”号作为按键输入结尾的分机号（例如访问分机的“跟我来”），可能不接收此设置。 正在添加annmsg_id字段。。。 正在添加remotealert_id字段。。。 正在添加toolate_id字段。。。 已经迁移过了 默认 已删除 正在删除annmsg字段。。。 正在删除remotealert字段。。。 正在删除toolate字段。。。 致命错误 首个可用频道 首个未离钩频道 搜寻 已经在使用中了   记忆性搜寻 将annmsg迁移到ids。。。 将remotealert迁移到ids。。。 将toolate迁移到ids。。。 迁移了%s个项目 正在迁移 没有annmsg字段？ 没有remotealert字段？ 没有toolate字段？ 无 完成 只在第一个可用的频道响铃 只在第一个不是离钩状态下的频道响铃——忽略呼叫等待 全部响铃 