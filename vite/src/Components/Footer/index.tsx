import "./styles.css";

interface FooterProps {
  children: string;
}

export default function Footer(props: FooterProps) {
  return (
    <>
      <div className="footer-div" />
      <hr />
      <p className="footer-paragraph">{props.children}</p>
    </>
  );
}
