/* Landing Page components — バキューム & ヒトイヌ 驚異のコラボ大体験会 */

const { useState, useRef, useEffect } = React;

// Resource resolver — supports both direct file paths (dev) and
// bundled blob URLs via window.__resources (standalone build).
const R = (id, fallback) => (typeof window !== 'undefined' && window.__resources && window.__resources[id]) || fallback;

/* ---------- visual primitives ---------- */

const Halftone = ({ color = "#1d8e8e", size = 14, opacity = 0.55, className = "", style = {} }) =>
<div className={"halftone " + className}
style={{
  position: "absolute", inset: 0, pointerEvents: "none",
  backgroundImage: `radial-gradient(${color} 22%, transparent 23%)`,
  backgroundSize: `${size}px ${size}px`,
  opacity, ...style
}} />;


const Rays = ({ className = "", style = {} }) =>
<div className={"rays " + className}
style={{
  position: "absolute", inset: 0, pointerEvents: "none",
  background: `conic-gradient(from 0deg,
           var(--accent-2) 0 4deg, transparent 4deg 22deg,
           var(--accent-2) 22deg 26deg, transparent 26deg 50deg,
           var(--accent-2) 50deg 53deg, transparent 53deg 80deg,
           var(--accent-2) 80deg 84deg, transparent 84deg 110deg,
           var(--accent-2) 110deg 114deg, transparent 114deg 140deg,
           var(--accent-2) 140deg 144deg, transparent 144deg 170deg,
           var(--accent-2) 170deg 174deg, transparent 174deg 200deg,
           var(--accent-2) 200deg 204deg, transparent 204deg 230deg,
           var(--accent-2) 230deg 234deg, transparent 234deg 260deg,
           var(--accent-2) 260deg 264deg, transparent 264deg 290deg,
           var(--accent-2) 290deg 294deg, transparent 294deg 320deg,
           var(--accent-2) 320deg 324deg, transparent 324deg 350deg,
           var(--accent-2) 350deg 354deg, transparent 354deg 360deg)`,
  mixBlendMode: "multiply",
  opacity: .55,
  ...style
}} />;


const Cloud = ({ style = {} }) =>
<svg viewBox="0 0 200 80" style={style} preserveAspectRatio="none">
    <path d="M10,60 Q0,40 20,35 Q15,15 40,18 Q45,2 70,10 Q85,-2 100,12 Q120,4 130,18 Q160,12 165,32 Q190,30 188,55 Q200,75 170,75 L25,75 Q-2,80 10,60 Z"
  fill="#fff" stroke="var(--ink)" strokeWidth="3" strokeLinejoin="round" />
  </svg>;


const Burst = ({ children, color = "var(--accent-3)", rotate = -3, size = "2.4rem", className = "" }) =>
<span className={"burst-text " + className} style={{ ...{
    display: "inline-block", transform: `rotate(${rotate}deg)`,

    color, fontSize: size, lineHeight: 1,
    WebkitTextStroke: "2px var(--ink)",
    textShadow: "4px 4px 0 var(--ink)",
    paintOrder: "stroke fill",
    letterSpacing: "0.01em", fontFamily: "\"Reggae One\""
  }, color: "rgb(224, 67, 43)" }}>{children}</span>;


const Speech = ({ children, tail = "bl", color = "#fff", className = "", style = {} }) =>
<div className={"speech " + className} style={{
  position: "relative", display: "inline-block",
  background: color, border: "3px solid var(--ink)", borderRadius: "22px",
  padding: "10px 16px", fontFamily: '"Reggae One", "Noto Sans JP", sans-serif',
  boxShadow: "4px 4px 0 var(--ink)",
  ...style
}}>
    {children}
    <span style={{
    position: "absolute", width: 18, height: 18, background: color,
    border: "3px solid var(--ink)", borderTop: "none", borderLeft: "none",
    transform: "rotate(45deg)",
    ...(tail === "bl" ? { left: 24, bottom: -12 } : {}),
    ...(tail === "br" ? { right: 24, bottom: -12 } : {}),
    ...(tail === "tl" ? { left: 24, top: -12, transform: "rotate(225deg)" } : {}),
    ...(tail === "tr" ? { right: 24, top: -12, transform: "rotate(225deg)" } : {})
  }} />
  </div>;


const SectionHead = ({ eyebrow, title, kicker, align = "left" }) =>
<div style={{ textAlign: align, marginBottom: 18, position: "relative", zIndex: 2 }}>
    {eyebrow &&
  <div style={{ display: "inline-block", background: "var(--ink)", color: "#fff",
    padding: "3px 10px", fontFamily: '"Bangers", "Reggae One", sans-serif',
    letterSpacing: "0.08em", fontSize: 13, marginBottom: 8, transform: "rotate(-1deg)" }}>
        {eyebrow}
      </div>
  }
    <h2 style={{
    margin: 0, fontFamily: '"Reggae One", "Noto Sans JP", sans-serif',
    fontSize: "2.1rem", lineHeight: 1.05, color: "var(--ink)",
    letterSpacing: "0.01em"
  }}>{title}</h2>
    {kicker && <div style={{ marginTop: 6, fontSize: 13, color: "var(--ink)", opacity: .7 }}>{kicker}</div>}
  </div>;


const ImgPlaceholder = ({ label = "photo", h = 180, style = {} }) =>
<div style={{
  position: "relative",
  background: `repeating-linear-gradient(45deg, #efeae0 0 10px, #fbf8f1 10px 20px)`,
  border: "3px solid var(--ink)", borderRadius: 8, height: h,
  display: "flex", alignItems: "center", justifyContent: "center",
  fontFamily: '"JetBrains Mono", monospace', fontSize: 12, color: "#777",
  boxShadow: "4px 4px 0 var(--ink)",
  ...style
}}>
    <div style={{ textAlign: "center" }}>
      <div style={{ fontSize: 18, marginBottom: 4 }}>📷</div>
      <div>{label}</div>
    </div>
  </div>;


/* ---------- HERO ---------- */
const Hero = () =>
<section data-screen-label="01 Hero" className="hero" style={{
  position: "relative", overflow: "hidden",
  background: "var(--accent-1)",
  padding: "24px 22px 60px",
  borderBottom: "4px solid var(--ink)"
}}>
    {/* halftone + rays */}
    <Halftone color="#ffffffaa" size={11} opacity={1} />
    <Rays />
    {/* clouds top */}
    <div style={{ position: "absolute", top: -6, right: -30, width: 220, height: 90, transform: "rotate(8deg)" }}>
      <Cloud style={{ width: "100%", height: "100%" }} />
    </div>
    <div style={{ position: "absolute", top: 80, left: -50, width: 180, height: 70, transform: "rotate(-10deg)" }}>
      <Cloud style={{ width: "100%", height: "100%" }} />
    </div>

    {/* content */}
    <div style={{ position: "relative", zIndex: 2 }}>
      <div style={{ marginTop: 30 }}>
        <Speech tail="bl" color="#fff" style={{ fontSize: 15 }}>
          6月28日に開催の<br />
          <span style={{ color: "var(--accent-3)", fontSize: 22 }}>ぴたけっと応援企画</span>
        </Speech>
      </div>

      <div style={{ margin: "38px 0 18px", textAlign: "left", lineHeight: 1.1 }}>
        <Burst size="3.1rem" rotate={-2}>バキューム</Burst>
        <div style={{ marginTop: 6 }}>
          <Burst size="2.4rem" rotate={1} color="var(--ink)">&</Burst>{" "}
          <Burst size="3.1rem" rotate={-1}>ヒトイヌ</Burst>
        </div>
        <div style={{ marginTop: 14 }}>
          <Burst size="2.0rem" rotate={-2} color="var(--accent-2)">驚異のコラボ</Burst>
        </div>
        <div style={{ marginTop: 6 }}>
          <Burst size="2.4rem" rotate={1}>大体験会</Burst>
        </div>
        <div style={{ marginTop: 14 }}>
          <Burst size="1.6rem" rotate={-1} color="var(--accent-3)">in 芳賀書店 6階</Burst>
        </div>
      </div>

      {/* organizers */}
      <div style={{ display: "flex", gap: 10, marginTop: 30, marginBottom: 18, justifyContent: "space-between" }}>
        <div style={{ flex: 1, textAlign: "center" }}>
          <div style={{
            height:86, borderRadius:"50%", overflow:"hidden",
            border:"3px solid var(--ink)", boxShadow:"4px 4px 0 var(--ink)",
            background:"#fff"
          }}>
            <img src={R("shun","assets/shun.png")} alt="Shun"
                 style={{width:"100%",height:"100%",objectFit:"cover",display:"block"}}/>
          </div>
          <div style={{ marginTop: 8, fontFamily: '"Reggae One", sans-serif', color: "var(--accent-3)", fontSize: 15 }}>
            担当: Shun
          </div>
        </div>
        <div style={{ flex: 1, textAlign: "center" }}>
          <div style={{
            height:86, borderRadius:"50%", overflow:"hidden",
            border:"3px solid var(--ink)", boxShadow:"4px 4px 0 var(--ink)",
            background:"#000"
          }}>
            <img src={R("rinrin","assets/rinrin.png")} alt="リンリン"
                 style={{width:"100%",height:"100%",objectFit:"cover",display:"block"}}/>
          </div>
          <div style={{ marginTop: 8, fontFamily: '"Reggae One", sans-serif', color: "var(--accent-3)", fontSize: 15 }}>
            担当: リンリン
          </div>
        </div>
      </div>

      {/* date card */}
      <div style={{
      background: "#fff", border: "3px solid var(--ink)", borderRadius: 14,
      padding: "16px 18px", marginTop: 14,
      boxShadow: "5px 5px 0 var(--ink)"
    }}>
        <div style={{
        display: "inline-block", background: "var(--ink)", color: "#fff",
        padding: "2px 10px", fontFamily: '"Bangers", sans-serif', letterSpacing: "0.1em", fontSize: 13,
        marginBottom: 10
      }}>OPEN DAYS</div>
        <div style={{ display: "flex", gap: 12, alignItems: "stretch" }}>
          <div style={{ flex: 1 }}>
            <div style={{ fontFamily: '"Reggae One", sans-serif', fontSize: 30, lineHeight: 1, color: "var(--accent-3)" }}>6<span style={{ fontSize: 18 }}>/</span>27</div>
            <div style={{ fontSize: 11, marginTop: 2, opacity: .7 }}>SAT</div>
            <div style={{ fontFamily: '"Reggae One", sans-serif', fontSize: 16, marginTop: 4 }}>10:00 – 19:00</div>
          </div>
          <div style={{ width: 3, background: "var(--ink)" }} />
          <div style={{ flex: 1 }}>
            <div style={{ fontFamily: '"Reggae One", sans-serif', fontSize: 30, lineHeight: 1, color: "var(--accent-3)" }}>6<span style={{ fontSize: 18 }}>/</span>28</div>
            <div style={{ fontSize: 11, marginTop: 2, opacity: .7 }}>SUN</div>
            <div style={{ fontFamily: '"Reggae One", sans-serif', fontSize: 16, marginTop: 4 }}>15:00 – 18:00</div>
          </div>
        </div>
      </div>

      <a href="#book" style={{ display: "block", marginTop: 22, textDecoration: "none" }}>
        <div style={{
        background: "var(--accent-3)", color: "#fff",
        border: "3px solid var(--ink)", borderRadius: 99, padding: "14px 22px",
        textAlign: "center", fontFamily: '"Reggae One", sans-serif', fontSize: 18,
        boxShadow: "5px 5px 0 var(--ink)"
      }}>
          詳細はこの下 ▼
        </div>
      </a>
    </div>
  </section>;


/* ---------- WHAT ---------- */
const SectionWhat = () =>
<section data-screen-label="02 What" style={{ padding: "56px 22px 48px", background: "var(--paper)", position: "relative", overflow: "hidden" }}>
    <SectionHead eyebrow="01 — WHAT" title={<>そもそも、<br />なにする会？</>} />
    <p style={{ fontSize: 15, lineHeight: 1.8, margin: "0 0 18px" }}>
      <b>バキュームベッド</b> と <b>ヒトイヌ</b> という 2 つのフェチが、<br />
      1 つの会場に集結する <b>2 日間限定</b> のコラボ体験会です。
    </p>
    <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 10 }}>
      <div style={{
        position:"relative",
        background:"#fff",
        border:"3px solid var(--ink)", borderRadius:8, height:150, overflow:"hidden",
        boxShadow:"4px 4px 0 var(--ink)"
      }}>
        <img src={R("vacuum","assets/vacuum.png")} alt="バキューム ベッド & キューブ"
             style={{width:"100%",height:"100%",objectFit:"cover",display:"block"}}/>
        <div style={{
          position:"absolute",left:6,bottom:6,
          background:"var(--ink)",color:"#fff",
          fontFamily:'"Bangers", sans-serif',fontSize:11,letterSpacing:".08em",
          padding:"2px 8px"
        }}>VACUUM</div>
      </div>
      <div style={{
        position:"relative",
        background:"#fff",
        border:"3px solid var(--ink)", borderRadius:8, height:150, overflow:"hidden",
        boxShadow:"4px 4px 0 var(--ink)"
      }}>
        <img src={R("hitoinu","assets/hitoinu.png")} alt="ヒトイヌ"
             style={{width:"100%",height:"100%",objectFit:"cover",display:"block"}}/>
        <div style={{
          position:"absolute",left:6,bottom:6,
          background:"var(--ink)",color:"#fff",
          fontFamily:'"Bangers", sans-serif',fontSize:11,letterSpacing:".08em",
          padding:"2px 8px"
        }}>HUMAN DOG</div>
      </div>
    </div>
    <div style={{ marginTop: 18, display: "flex", gap: 8, flexWrap: "wrap" }}>
      {["10min/max × 2set", "完全予約制", "現金のみ", "更衣室あり", "クロークあり"].map((t) =>
    <span key={t} style={{
      background: "#fff", border: "2.5px solid var(--ink)", borderRadius: 99,
      padding: "5px 12px", fontSize: 12, fontFamily: '"Reggae One", sans-serif',
      boxShadow: "2px 2px 0 var(--ink)"
    }}>#{t}</span>
    )}
    </div>
  </section>;


/* ---------- WHEN ---------- */
const SectionWhen = () => {
  const [day, setDay] = useState("27");
  const slots27 = [
  ["第1部", "10:00 – 10:50"], ["第2部", "11:00 – 11:50"],
  ["第3部", "12:00 – 12:50"], ["第4部", "14:00 – 14:50"],
  ["第5部", "15:00 – 15:50"], ["第6部", "16:00 – 16:50"],
  ["第7部", "17:00 – 17:50"], ["第8部", "18:00 – 18:50"]];

  const slots28 = [
  ["第1部", "15:00 – 15:50"], ["第2部", "16:00 – 16:50"],
  ["第3部", "17:00 – 17:50"], ["第4部", "18:00 – 18:50"]];

  const slots = day === "27" ? slots27 : slots28;
  return (
    <section data-screen-label="03 When" style={{ padding: "56px 22px 48px", background: "var(--accent-1-soft)", position: "relative", overflow: "hidden", borderTop: "4px solid var(--ink)", borderBottom: "4px solid var(--ink)" }}>
      <Halftone color="#ffffff" size={10} opacity={.55} />
      <div style={{ position: "relative", zIndex: 2 }}>
        <div style={{ marginBottom: 14, textAlign: "right" }}>
          <Speech tail="br" color="#fff" style={{ fontSize: 15 }}>いつ、やる？</Speech>
        </div>
        <SectionHead eyebrow="02 — WHEN" title={<>体験タイムテーブル</>} kicker="予約サイトからの予約者のみ体験可。当日見学枠も予約サイトでご確認ください。" />

        {/* day tabs */}
        <div style={{ display: "flex", gap: 0, marginBottom: 14, border: "3px solid var(--ink)", borderRadius: 14, overflow: "hidden", background: "#fff", boxShadow: "4px 4px 0 var(--ink)" }}>
          {[["27", "DAY 1 ・ 6/27 (SAT)"], ["28", "DAY 2 ・ 6/28 (SUN)"]].map(([k, l]) =>
          <button key={k} onClick={() => setDay(k)} style={{
            flex: 1, padding: "12px 8px", border: "none",
            background: day === k ? "var(--accent-3)" : "transparent",
            color: day === k ? "#fff" : "var(--ink)",
            fontFamily: '"Reggae One", sans-serif', fontSize: 13,
            cursor: "pointer", borderRight: k === "27" ? "3px solid var(--ink)" : "none"
          }}>{l}</button>
          )}
        </div>

        {/* slot list */}
        <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 8 }}>
          {slots.map(([n, t], i) =>
          <div key={i} style={{
            background: "#fff", border: "3px solid var(--ink)", borderRadius: 10,
            padding: "10px 12px", boxShadow: "3px 3px 0 var(--ink)"
          }}>
              <div style={{ fontFamily: '"Reggae One", sans-serif', fontSize: 14, color: "var(--accent-3)" }}>{n}</div>
              <div style={{ fontFamily: '"Reggae One", sans-serif', fontSize: 14, marginTop: 2 }}>{t}</div>
            </div>
          )}
        </div>

        <div style={{
          marginTop: 18, background: "#fff", border: "3px dashed var(--ink)", borderRadius: 10,
          padding: "12px 14px", fontSize: 13, lineHeight: 1.7
        }}>
          ・体験内容は <b>10min/max × 2set</b> を想定。<br />
          ・予約時間を過ぎると体験できない場合があります。<br />
          ・体験後はご自身で内部の簡易拭き清掃をお願いします。
        </div>
      </div>
    </section>);

};

/* ---------- WHERE ---------- */
const SectionWhere = () =>
<section data-screen-label="04 Where" style={{ padding: "56px 22px 48px", background: "var(--paper)" }}>
    <div style={{ marginBottom: 14 }}>
      <Speech tail="bl" color="#fff" style={{ fontSize: 15 }}>どこに行けば いい？</Speech>
    </div>
    <SectionHead eyebrow="03 — WHERE" title={<>会場 / Access</>} />

    <div style={{
    background: "#fff", border: "3px solid var(--ink)", borderRadius: 14,
    padding: "16px 18px", boxShadow: "5px 5px 0 var(--ink)", marginBottom: 18
  }}>
      <div style={{ fontFamily: '"Reggae One", sans-serif', fontSize: 20 }}>芳賀書店ビル 6F</div>
      <div style={{ fontSize: 13, marginTop: 6, lineHeight: 1.7 }}>
        〒101-0051<br />
        東京都千代田区神田神保町 2-7
      </div>
      <div style={{ marginTop: 12, display: "inline-block",
      background: "var(--accent-2)", border: "3px solid var(--ink)",
      padding: "6px 12px", borderRadius: 10,
      fontFamily: '"Reggae One", sans-serif', fontSize: 15,
      transform: "rotate(-2deg)",
      boxShadow: "3px 3px 0 var(--ink)"
    }}>
        ※ 神保町駅 A1 出口 <span style={{ color: "var(--accent-3)" }}>徒歩 0 秒</span>
      </div>
    </div>

    <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 10, marginBottom: 18 }}>
      <div style={{
        position:"relative",
        background:"#fff",
        border:"3px solid var(--ink)", borderRadius:8, height:180, overflow:"hidden",
        boxShadow:"4px 4px 0 var(--ink)"
      }}>
        <img src={R("floor","assets/floor.png")} alt="フロア見取り図 (6F)"
             style={{width:"100%",height:"100%",objectFit:"contain",display:"block",background:"#fff"}}/>
        <div style={{
          position:"absolute",left:6,bottom:6,
          background:"var(--ink)",color:"#fff",
          fontFamily:'"Bangers", sans-serif',fontSize:10,letterSpacing:".08em",
          padding:"2px 8px"
        }}>FLOOR / 6F</div>
      </div>
      <div style={{
        position:"relative",
        background:"#fff",
        border:"3px solid var(--ink)", borderRadius:8, height:180, overflow:"hidden",
        boxShadow:"4px 4px 0 var(--ink)"
      }}>
        <img src={R("entrance","assets/entrance.png")} alt="入口の写真"
             style={{width:"100%",height:"100%",objectFit:"cover",display:"block"}}/>
        <div style={{
          position:"absolute",left:6,bottom:6,
          background:"var(--ink)",color:"#fff",
          fontFamily:'"Bangers", sans-serif',fontSize:10,letterSpacing:".08em",
          padding:"2px 8px"
        }}>ENTRANCE</div>
      </div>
    </div>

    <div style={{
    background: "#fff", border: "3px solid var(--ink)", borderRadius: 14,
    padding: "14px 16px", fontSize: 13, lineHeight: 1.85,
    boxShadow: "4px 4px 0 var(--ink)"
  }}>
      <b style={{ fontFamily: '"Reggae One", sans-serif', fontSize: 15 }}>会場設備</b>
      <div style={{ marginTop: 6 }}>
        ・更衣室、クロークあり（スーツケースもOK）<br />
        ・お手洗い 1 か所、温水洗浄便座<br />
        ・イス、テーブルあり<br />
        ・室内禁煙（同フロアベランダに喫煙スペース）<br />
        ・冷房、換気設備完備
      </div>
    </div>
  </section>;


/* ---------- BRING + WEAR ---------- */
const SectionBringWear = () =>
<section data-screen-label="05 BringWear" style={{ padding: "56px 22px 48px", background: "var(--accent-2-soft)", position: "relative", overflow: "hidden", borderTop: "4px solid var(--ink)", borderBottom: "4px solid var(--ink)" }}>
    <Halftone color="var(--ink)" size={12} opacity={.08} />
    <div style={{ position: "relative", zIndex: 2 }}>
      <div style={{ marginBottom: 14, textAlign: "right" }}>
        <Speech tail="br" color="#fff" style={{ fontSize: 15 }}>何を持って・<br />何を着ていく？</Speech>
      </div>
      <SectionHead eyebrow="04 — BRING & WEAR" title={<>持ち物と服装</>} />

      {/* bring */}
      <div style={{
      background: "#fff", border: "3px solid var(--ink)", borderRadius: 14,
      padding: "16px 18px", boxShadow: "5px 5px 0 var(--ink)", marginBottom: 16
    }}>
        <div style={{ display: "inline-block", background: "var(--ink)", color: "#fff",
        padding: "3px 10px", fontFamily: '"Bangers", sans-serif', fontSize: 13, letterSpacing: ".08em", marginBottom: 10 }}>
          🎒 BRING
        </div>
        <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr 1fr", gap: 8 }}>
          {[
        ["全員", "タオル / お飲み物"],
        ["コスチューム着用", "コスチューム"],
        ["普段着", "汚れてもいい下着・靴下"]].
        map(([h, b]) =>
        <div key={h} style={{
          background: "var(--paper)", border: "2.5px solid var(--ink)", borderRadius: 8, padding: "8px 10px"
        }}>
              <div style={{ fontFamily: '"Reggae One", sans-serif', fontSize: 11, color: "var(--accent-3)", marginBottom: 4 }}>{h}</div>
              <div style={{ fontSize: 12, lineHeight: 1.5 }}>{b}</div>
            </div>
        )}
        </div>
      </div>

      {/* wear */}
      <div style={{
      background: "#fff", border: "3px solid var(--ink)", borderRadius: 14,
      padding: "16px 18px", boxShadow: "5px 5px 0 var(--ink)"
    }}>
        <div style={{ display: "inline-block", background: "var(--ink)", color: "#fff",
        padding: "3px 10px", fontFamily: '"Bangers", sans-serif', fontSize: 13, letterSpacing: ".08em", marginBottom: 10 }}>
          👕 WEAR
        </div>
        <div style={{ display: "flex", flexWrap: "wrap", gap: 6, marginBottom: 12 }}>
          {[
        "ラバースーツ", "全身タイツ", "水着", "レオタード", "下着+靴下", "スポーツウェア", "平服OK"].
        map((t) =>
        <span key={t} style={{
          background: "var(--accent-1)", color: "#fff", border: "2.5px solid var(--ink)", borderRadius: 99,
          padding: "5px 11px", fontSize: 12, fontFamily: '"Reggae One", sans-serif',
          boxShadow: "2px 2px 0 var(--ink)"
        }}>OK ・ {t}</span>
        )}
        </div>
        <ul style={{ margin: 0, paddingLeft: 20, fontSize: 12, lineHeight: 1.85 }}>
          <li><b>ソックスは必須</b>（つま先保護のため）</li>
          <li>局部のはみだしは <b style={{ color: "var(--accent-3)" }}>NG</b></li>
          <li>金属製品 (特に真鍮) ・鋭利 / 硬い物は外す（コルセット・チョーカー・手枷・指輪等）</li>
          <li>万一の破損も参加費に免責費用が含まれます</li>
          <li>故意の破損と判断された場合は別途修理費を請求します</li>
        </ul>
      </div>
    </div>
  </section>;


/* ---------- CHECK ---------- */
const SectionCheck = () =>
<section data-screen-label="06 Check" style={{ padding: "56px 22px 48px", background: "var(--paper)" }}>
    <div style={{ marginBottom: 14 }}>
      <Speech tail="bl" color="#fff" style={{ fontSize: 15 }}>参加できるか<br />チェック！</Speech>
    </div>
    <SectionHead eyebrow="05 — REQUIREMENTS" title={<>参加条件</>} />

    <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 10, marginBottom: 14 }}>
      {[
    ["体重", "85kg 以下", "⚖"],
    ["身長", "2m 以下", "📏"],
    ["年齢", "50歳 以下", "🎂"],
    ["健康", "感染症なし", "🩺"]].
    map(([k, v, e]) =>
    <div key={k} style={{
      background: "#fff", border: "3px solid var(--ink)", borderRadius: 14,
      padding: "14px 12px", textAlign: "center",
      boxShadow: "4px 4px 0 var(--ink)"
    }}>
          <div style={{ fontSize: 28, marginBottom: 4 }}>{e}</div>
          <div style={{ fontSize: 11, opacity: .65, fontFamily: '"Bangers", sans-serif', letterSpacing: ".08em" }}>{k}</div>
          <div style={{ fontFamily: '"Reggae One", sans-serif', fontSize: 18, marginTop: 2, color: "var(--accent-3)" }}>{v}</div>
        </div>
    )}
    </div>

    <div style={{
    background: "#fff", border: "3px dashed var(--ink)", borderRadius: 10,
    padding: "12px 14px", fontSize: 13, lineHeight: 1.75
  }}>
      <b>共通条件</b><br />
      ・他の方の趣味趣向を否定しない事<br />
      ・熱・咳・風邪症状がなく健康である事<br />
      ・過去 1 週間以内に感染症を患っていない事<br />
      ・心肺機能が正常で健康である事
    </div>
  </section>;


/* ---------- WARNING ---------- */
const SectionWarning = () =>
<section data-screen-label="07 Warning" style={{ padding: "56px 22px 48px", background: "var(--accent-3)", color: "#fff", position: "relative", overflow: "hidden", borderTop: "4px solid var(--ink)", borderBottom: "4px solid var(--ink)" }}>
    <Halftone color="#ffffff" size={12} opacity={.25} />
    <Rays style={{ opacity: .35 }} />
    <div style={{ position: "relative", zIndex: 2 }}>
      <div style={{ textAlign: "center", marginBottom: 8 }}>
        <span style={{
        display: "inline-block", background: "var(--accent-2)", color: "var(--ink)",
        border: "3px solid var(--ink)", padding: "4px 14px",
        fontFamily: '"Bangers", sans-serif', fontSize: 22, letterSpacing: ".1em",
        transform: "rotate(-4deg)",
        boxShadow: "4px 4px 0 var(--ink)"
      }}>!! READ ME !!</span>
      </div>
      <h2 style={{
      margin: "20px 0 14px", textAlign: "center",
      fontFamily: '"Reggae One", sans-serif', fontSize: "2.2rem", lineHeight: 1.05,
      WebkitTextStroke: "1.5px var(--ink)", textShadow: "4px 4px 0 var(--ink)",
      paintOrder: "stroke fill", color: "var(--accent-2)"
    }}>バキュームベッド<br />注意点</h2>

      <div style={{
      background: "#fff", color: "var(--ink)",
      border: "3px solid var(--ink)", borderRadius: 14,
      padding: "16px 18px", boxShadow: "5px 5px 0 var(--ink)"
    }}>
        <ol style={{ margin: 0, paddingLeft: 22, fontSize: 13, lineHeight: 1.9 }}>
          <li><b>重大な事故</b> に繋がる恐れあり。必ずスタッフ指示に従う</li>
          <li>1 回の最長体験時間は <b>10 分</b></li>
          <li>マウスピース型ではしゃべれません。<b>大きな音を出さない</b></li>
          <li>マウスピース型は支給の<b>シリコン耳栓</b>を着用</li>
          <li>吸引・開放等の操作は<b>スタッフのみ</b>、機械には触れない</li>
          <li>閉所恐怖症の方は<b>顔が出るタイプ</b>を推奨</li>
          <li><b>呼吸口は絶対にふさがない</b></li>
          <li><b>酒気帯び</b>での体験はお断り</li>
          <li>当日お渡しする<b>同意書にサイン必須</b></li>
          <li>既往歴がある際は事前申告</li>
          <li><b>ラテックスアレルギー</b>の方はご遠慮ください</li>
        </ol>
      </div>
    </div>
  </section>;


/* ---------- RULES (accordion) ---------- */
const Accordion = ({ items }) => {
  const [open, setOpen] = useState(null);
  return (
    <div style={{
      background: "#fff", border: "3px solid var(--ink)", borderRadius: 14,
      boxShadow: "5px 5px 0 var(--ink)", overflow: "hidden"
    }}>
      {items.map((it, i) => {
        const isOpen = open === i;
        return (
          <div key={i} style={{ borderBottom: i < items.length - 1 ? "2.5px solid var(--ink)" : "none" }}>
            <button onClick={() => setOpen(isOpen ? null : i)} style={{
              all: "unset", display: "flex", width: "100%", alignItems: "center",
              justifyContent: "space-between", padding: "14px 16px", cursor: "pointer",
              fontFamily: '"Reggae One", sans-serif', fontSize: 15,
              background: isOpen ? "var(--accent-2-soft)" : "transparent",
              boxSizing: "border-box"
            }}>
              <span>■ {it.title}</span>
              <span style={{
                width: 24, height: 24, borderRadius: "50%",
                border: "2.5px solid var(--ink)",
                display: "flex", alignItems: "center", justifyContent: "center",
                fontSize: 14, background: "var(--accent-2)",
                transform: isOpen ? "rotate(45deg)" : "none",
                transition: "transform .15s"
              }}>＋</span>
            </button>
            {isOpen &&
            <div style={{ padding: "4px 18px 16px", fontSize: 13, lineHeight: 1.85 }}>
                {it.body}
              </div>
            }
          </div>);

      })}
    </div>);

};

const SectionRules = () => {
  const items = [
  { title: "ドレスコード", body: <>
      ・平服での参加も可能です。<br />
      ・フェティッシュ衣装 (ラバー、全身タイツ、レオタード、水着) も可能。<br />
      ・局部は見えないようにしてください。<br />
      ・更衣室あります。
    </> },
  { title: "予約枠 / 当日見学", body: <>
      ・予約サイトからのみ受付。主催・スタッフ個人では受け付けていません。<br />
      ・予約者は入場カウンターにて予約完了メールを提示、またはお名前をお伝えください。
    </> },
  { title: "決済方法", body: "・現金のみ。お釣りがないようにご協力ください。" },
  { title: "再入場", body: <>
      ・再入場時は入場の際にお渡しする整理番号を係員に提示ください。<br />
      ・整理番号の確認ができない場合、再入場をお断りします。<br />
      ・再入場できない場合の払い戻しはいたしかねます。
    </> },
  { title: "クローク利用", body: <>
      ・原則お一人様 1 個。途中での出し入れは原則お断り。<br />
      ・整理券で管理。取出し時に整理券の確認をします。<br />
      ・お預け中の盗難・破損・汚損は免責です。
    </> },
  { title: "室内禁煙", body: "・会場内は禁煙。同フロアベランダをご利用ください (定員 3〜4 名)。" },
  { title: "映像・写真 撮影", body: <>
      ・体験会の写真撮影はスタッフ以外原則 NG。ご自身を撮影する事は構いません。<br />
      ・他参加者と撮影する際は必ず本人の許諾を得てから。<br />
      ・スタッフ撮影分は後日サイトのギャラリーに掲載される可能性があります。
    </> },
  { title: "室内飲食", body: "・飲食自由。ゴミは持ち帰り。こぼし注意。酒類はご遠慮ください (体験内容的にも)。" },
  { title: "触れ合い", body: <>
      ・体験中の方が「おさわりOK」の場合のみ可能。必ずコミュニケーションを取り、事故・怪我のないように。<br />
      ・マッサージ可の場合は昇天させないよう留意 (清掃が発生するため)。
    </> },
  { title: "持ち物の管理", body: "・衣服・貴重品・荷物の管理はご自身でお願いします (主催側は責任を負えません)。" },
  { title: "免責", body: "・本イベントで発生した破損・事故・事件等は主催および関係者は責任をとれません。自己管理・自己責任にて。" },
  { title: "外出時", body: "・外出・退室される際は可能な限り平服に着替えをお願いします。" },
  { title: "その他", body: <>
      ・皆さんが気持ちよく体験できるよう可能な限り身体を清潔に。<br />
      ・バキュームベッド・キューブはシリコンオイル塗布。髪・体・コスチュームに付着します。<br />
      ・前の利用者様の汗などが拭ききれない場合があります。
    </> }];

  return (
    <section data-screen-label="08 Rules" style={{ padding: "56px 22px 48px", background: "var(--paper)" }}>
      <div style={{ marginBottom: 14, textAlign: "right" }}>
        <Speech tail="br" color="#fff" style={{ fontSize: 15 }}>会場での お約束</Speech>
      </div>
      <SectionHead eyebrow="06 — RULES" title={<>注意事項 ・<br />会場内ルール</>} />
      <Accordion items={items} />
    </section>);

};

/* ---------- CONTACT / CTA ---------- */
const SectionCTA = () =>
<section id="book" data-screen-label="09 CTA" style={{ padding: "60px 22px 48px", background: "var(--accent-1)", color: "#fff", position: "relative", overflow: "hidden", borderTop: "4px solid var(--ink)" }}>
    <Halftone color="#fff" size={12} opacity={.55} />
    <Rays />
    <div style={{ position: "absolute", top: -10, left: -40, width: 200, height: 80, transform: "rotate(-8deg)" }}>
      <Cloud style={{ width: "100%", height: "100%" }} />
    </div>

    <div style={{ position: "relative", zIndex: 2, textAlign: "center", paddingTop: 40 }}>
      <div style={{ display: "inline-block" }}>
        <Burst size="2.6rem" rotate={-3}>お申込み</Burst>
      </div>
      <div style={{ marginTop: 6 }}>
        <Burst size="2.2rem" rotate={2}>お待ちしてます！！</Burst>
      </div>

      <a href="https://tickets.alt-fetish.com/" style={{ display: "block", marginTop: 30, textDecoration: "none" }}>
        <div style={{
        background: "var(--accent-2)", color: "var(--ink)",
        border: "3px solid var(--ink)", borderRadius: 99, padding: "18px 22px",
        textAlign: "center", fontFamily: '"Reggae One", sans-serif', fontSize: 20,
        boxShadow: "6px 6px 0 var(--ink)"
      }}>
          ▶ 予約サイトへ
        </div>
      </a>
      <div style={{
      marginTop: 34, background: "#fff", color: "var(--ink)",
      border: "3px solid var(--ink)", borderRadius: 14,
      padding: "16px 18px", textAlign: "left",
      boxShadow: "5px 5px 0 var(--ink)"
    }}>
        <div style={{ display: "inline-block", background: "var(--ink)", color: "#fff",
        padding: "3px 10px", fontFamily: '"Bangers", sans-serif', fontSize: 13, letterSpacing: ".08em", marginBottom: 10 }}>
          ✉ CONTACT
        </div>
        <div style={{ fontSize: 14, lineHeight: 1.95 }}>
          ・Shun &nbsp;<b>@shun_rubber</b><br />
          ・リンリン &nbsp;<b>@rinrin_rubber</b><br />
          ・ALT-FETISH &nbsp;<b>@ALTFETISH</b>
        </div>
      </div>
    </div>
  </section>;


/* ---------- FOOTER ---------- */
const Footer = () =>
<footer data-screen-label="10 Footer" style={{ padding: "30px 22px 40px", background: "var(--ink)", color: "#ddd" }}>
    <div style={{ fontFamily: '"Reggae One", sans-serif', fontSize: 14, color: "#fff", marginBottom: 10 }}>
      バキューム & ヒトイヌ 大体験会
    </div>
    <div style={{ fontSize: 11, lineHeight: 1.85, opacity: .8 }}>
      ※ 名称利用については事前に許可をいただいています。<br />
      ※『ぴたけっと』とは無関係です。ぴたけっとへのお問い合わせはご遠慮ください。<br /><br />
      主催: バキューム&ヒトイヌ&ラバー体験会実行チーム<br />
      © 2026 fatigue,Inc./ALT-FETISH.com
    </div>
  </footer>;


/* ---------- PAGE ASSEMBLY ---------- */
const LP = () =>
<article className="phone">
    <Hero />
    <SectionWhat />
    <SectionWhen />
    <SectionWhere />
    <SectionBringWear />
    <SectionCheck />
    <SectionWarning />
    <SectionRules />
    <SectionCTA />
    <Footer />
  </article>;


Object.assign(window, { LP });
